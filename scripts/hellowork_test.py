"""
ハローワーク スクレイピング（Playwright版）
沖縄×介護・福祉求人を全ページ取得してJSONに保存する
"""
import asyncio
import json
import re
from playwright.async_api import async_playwright

OUTPUT_FILE = "scripts/hellowork_okinawa_kaigo.json"


def clean_job(job: dict) -> dict:
    cleaned = {}
    for k, v in job.items():
        # キーの正規化
        key = k.replace("\n（手当等を含む）", "").strip()
        # 値のクリーニング
        val = v.replace("\n職種解説", "").strip()
        val = re.sub(r"\n二次元\nバーコード$", "", val).strip()
        val = val.replace("画像あり", "").strip()
        cleaned[key] = val
    return cleaned


async def extract_jobs(page) -> list[dict]:
    jobs = await page.evaluate("""
        () => {
            const results = [];
            const heads  = document.querySelectorAll('.kyujin_head_date');
            const kbns   = document.querySelectorAll('.kyujin_head_kbn');
            const bodies = document.querySelectorAll('tr.kyujin_body');

            bodies.forEach((body, i) => {
                const job = {};

                if (heads[i]) {
                    heads[i].querySelectorAll('.flex.nowrap.mr1').forEach(item => {
                        const parts = item.querySelectorAll(':scope > div');
                        if (parts.length >= 2) {
                            const key = parts[0].innerText.trim();
                            const val = parts[1].innerText.trim().replace('：', '');
                            job[key] = val;
                        }
                    });
                }

                if (kbns[i]) {
                    const labels = Array.from(kbns[i].querySelectorAll('.bg_label div'))
                        .map(l => l.innerText.trim()).filter(Boolean);
                    job['雇用形態'] = [...new Set(labels)].join('/');
                    const area = kbns[i].querySelector('.disp_inline_block');
                    if (area) job['就業都道府県'] = area.innerText.trim();
                }

                body.querySelectorAll('table.noborder tr').forEach(row => {
                    const label = row.querySelector('.label_col span')?.innerText?.trim();
                    const value = row.querySelector('.data_col')?.innerText?.trim();
                    if (label && value) job[label] = value;
                });

                results.push(job);
            });

            return results;
        }
    """)
    return [clean_job(j) for j in jobs]


async def scrape_hellowork():
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        page = await browser.new_page()

        print("ハローワーク 求人検索ページにアクセス中...")
        await page.goto(
            "https://www.hellowork.mhlw.go.jp/kensaku/GECA110010.do?action=initDisp&screenId=GECA110010",
            timeout=30000
        )
        await page.wait_for_timeout(2000)

        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")

        print("介護カテゴリを選択中...")
        await page.evaluate("""
            () => {
                const main = document.querySelector('input[name="daiEasyShokusyuBox"][value="09"]');
                if (main) main.checked = true;
                const sub = document.querySelector('input[name="easyShokusyuBox"][value="0900"]');
                if (sub) sub.checked = true;
            }
        """)

        print("都道府県（沖縄）を設定中...")
        await page.evaluate("""
            () => {
                const hidden = document.getElementById('ID_todohukenHidden');
                if (hidden) hidden.value = '47';
            }
        """)

        print("検索実行中...")
        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")
        await page.click('button[name="searchBtn"]')
        await page.wait_for_timeout(3000)

        # 総件数・総ページ数を取得
        total = await page.evaluate("""
            () => {
                const el = document.querySelector('input[name="kyujinkensu"]');
                return el ? parseInt(el.value) : 0;
            }
        """)
        per_page = 30
        total_pages = (total + per_page - 1) // per_page
        print(f"合計 {total} 件 / {total_pages} ページ")

        all_jobs = []

        for current_page in range(1, total_pages + 1):
            jobs = await extract_jobs(page)
            all_jobs.extend(jobs)
            print(f"  ページ {current_page}/{total_pages}: {len(jobs)} 件取得（累計 {len(all_jobs)} 件）")

            if current_page >= total_pages:
                break

            # ページ遷移前にデバッグ情報を取得（最初の1回のみ）
            if current_page == 1:
                debug = await page.evaluate("""
                    () => {
                        const forms = Array.from(document.forms).map(f => f.id + ':' + f.action);
                        const fwFuncs = Object.keys(window).filter(k => /fwList|pageList|goPage|dispPage/i.test(k));
                        const fwInputs = Array.from(document.querySelectorAll('input[name^="fwList"]')).map(e => e.name + '=' + e.value);
                        return JSON.stringify({forms, fwFuncs, fwInputs});
                    }
                """)
                import json as _j; d = _j.loads(debug)
                print(f"\nフォーム一覧: {d['forms']}")
                print(f"fwList関連JS関数: {d['fwFuncs']}")
                print(f"fwList系hidden fields: {d['fwInputs']}")

            # fwListNowPage を更新してフォーム送信
            submitted = await page.evaluate(f"""
                () => {{
                    const nowPage = document.querySelector('input[name="fwListNowPage"]');
                    if (!nowPage) return 'fwListNowPage not found';
                    nowPage.value = '{current_page + 1}';
                    const form = document.getElementById('ID_form_1');
                    if (!form) return 'ID_form_1 not found';
                    form.submit();
                    return 'submitted';
                }}
            """)
            print(f"  送信結果: {submitted}")
            await page.wait_for_load_state('domcontentloaded')
            await page.wait_for_timeout(1500)
            print(f"  遷移後URL: {page.url}")

        print(f"\n合計 {len(all_jobs)} 件取得完了")

        with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
            json.dump(all_jobs, f, ensure_ascii=False, indent=2)
        print(f"保存完了: {OUTPUT_FILE}")

        # サンプル表示
        print("\n=== サンプル（最初の2件）===")
        for job in all_jobs[:2]:
            print(json.dumps(job, ensure_ascii=False, indent=2))
            print("---")

        await browser.close()


if __name__ == "__main__":
    asyncio.run(scrape_hellowork())
