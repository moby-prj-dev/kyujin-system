"""
ハローワーク スクレイピングテスト（Playwright版）
沖縄×介護・福祉求人を取得する
"""
import asyncio
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

        # ページロード時に表示されるmomモーダル（overlay含む）を全て閉じる
        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")

        print(f"タイトル: {await page.title()}")

        # 全カテゴリのvalue値とラベルを確認
        categories = await page.evaluate("""
            () => {
                const cbs = document.querySelectorAll('input[name="daiEasyShokusyuBox"]');
                return Array.from(cbs).map(cb => {
                    const lbl = document.querySelector(`label[for="${cb.id}"]`);
                    return cb.value + ': ' + (lbl ? lbl.innerText.trim() : '?');
                });
            }
        """)
        print("カテゴリ一覧:", categories)

        # 介護・福祉のvalueを特定してモーダルを開く
        kaigo_value = next((c.split(':')[0] for c in categories if '介護' in c), None)
        print(f"介護・福祉 value: {kaigo_value}")

        if kaigo_value:
            print("介護カテゴリのモーダルを開いています...")
            # PlaywrightのクリックはインターセプトされるのでJSで直接クリック
            result = await page.evaluate(f"""
                () => {{
                    const cb = document.querySelector('input[name="daiEasyShokusyuBox"][value="{kaigo_value}"]');
                    if (!cb) return 'input not found';
                    const label = document.querySelector(`label[for="${{cb.id}}"]`);
                    if (!label) return `label not found for ${{cb.id}}`;
                    label.click();
                    return `clicked: ${{cb.id}}`;
                }}
            """)
            print(f"ラベルクリック結果: {result}")
            await page.wait_for_timeout(1500)

            # モーダルの底部HTML（決定ボタン確認）
            modal_info = await page.evaluate("""
                () => {
                    const modal = document.querySelector('.modal_content.mom');
                    if (!modal) return 'モーダルなし';
                    return modal.innerHTML.slice(-800);
                }
            """)
            print(f"モーダル底部HTML: {modal_info}")

            # 「こだわらない」をチェックしてsaveEasyShokusyuModalで確定
            confirm_result = await page.evaluate(f"""
                () => {{
                    const nodawari = document.querySelector('input[name="modalTmpEasyShokusyuBox"][value="{kaigo_value}00"]');
                    if (nodawari) nodawari.checked = true;

                    if (typeof saveEasyShokusyuModal === 'function') {{
                        saveEasyShokusyuModal('{kaigo_value}');
                        return `saveEasyShokusyuModal('{kaigo_value}') 呼び出し成功`;
                    }}
                    // フォールバック: #ID_saveBtnをクリック
                    const btn = document.querySelector('#ID_saveBtn');
                    if (btn) {{ btn.click(); return '決定ボタンクリック'; }}
                    return `失敗 / nodawari=${{!!nodawari}}`;
                }}
            """)
            print(f"モーダル確定: {confirm_result}")
            await page.wait_for_timeout(500)

        # 都道府県（沖縄=47）を設定
        # ID_todohukenHiddenはtype=hiddenなのでJSで直接セット
        print("都道府県（沖縄）を設定中...")
        try:
            await page.evaluate("""
                () => {
                    const sel = document.querySelector('select[name="todohuken"]');
                    if (sel) {
                        sel.value = '47';
                        sel.dispatchEvent(new Event('change', {bubbles: true}));
                        return;
                    }
                    const hidden = document.getElementById('ID_todohukenHidden');
                    if (hidden) {
                        hidden.value = '47';
                        hidden.dispatchEvent(new Event('change', {bubbles: true}));
                    }
                }
            """)
            await page.wait_for_timeout(500)
        except Exception as e:
            print(f"都道府県設定エラー: {e}")

        # 検索ボタンをクリック（momモーダルが再表示されていれば再度消す）
        print("検索実行中...")
        try:
            await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")
            await page.click('button[name="searchBtn"]')
            await page.wait_for_timeout(3000)
        except Exception as e:
            print(f"検索ボタンエラー: {e}")

        print(f"結果ページタイトル: {await page.title()}")
        print(f"URL: {page.url}")

        # 結果テキストを確認
        content = await page.inner_text('body')
        print(f"\n--- ページ全文字数: {len(content)} ---")
        print("\n--- 後半2000文字（結果がある場合はここに表示）---")
        print(content[-2000:])

        # 求人件数を探す（ハローワークの件数表示要素）
        count_el = await page.query_selector('#ID_total, #ID_count, .count, [id*="Count"], [id*="Total"]')
        if count_el:
            print(f"\n件数: {await count_el.inner_text()}")
        else:
            print("\n件数要素: 見つからず")

        await browser.close()

if __name__ == "__main__":
    asyncio.run(scrape_hellowork())
