"""
ハローワーク スクレイピングテスト（Playwright版）
沖縄×介護・福祉求人を取得する
"""
import asyncio
import json
from playwright.async_api import async_playwright

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
        await page.wait_for_timeout(300)

        print("都道府県（沖縄）を設定中...")
        await page.evaluate("""
            () => {
                const hidden = document.getElementById('ID_todohukenHidden');
                if (hidden) hidden.value = '47';
            }
        """)
        await page.wait_for_timeout(300)

        print("検索実行中...")
        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")
        await page.click('button[name="searchBtn"]')
        await page.wait_for_timeout(3000)

        # 求人データを構造化して取得
        jobs = await page.evaluate("""
            () => {
                const results = [];
                const heads  = document.querySelectorAll('.kyujin_head_date');
                const kbns   = document.querySelectorAll('.kyujin_head_kbn');
                const bodies = document.querySelectorAll('tr.kyujin_body');

                bodies.forEach((body, i) => {
                    const job = {};

                    // 受付年月日・紹介期限日
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

                    // 雇用形態・都道府県
                    if (kbns[i]) {
                        const labels = Array.from(kbns[i].querySelectorAll('.bg_label div'))
                            .map(l => l.innerText.trim()).filter(Boolean);
                        job['雇用形態'] = [...new Set(labels)].join('/');
                        const area = kbns[i].querySelector('.disp_inline_block');
                        if (area) job['就業都道府県'] = area.innerText.trim();
                    }

                    // 左・右テーブルのラベル-値ペア
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

        print(f"\n取得件数（1ページ目）: {len(jobs)} 件")
        print("\n=== 最初の3件 ===")
        for job in jobs[:3]:
            print(json.dumps(job, ensure_ascii=False, indent=2))
            print("---")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(scrape_hellowork())
