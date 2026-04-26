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

        # 介護カテゴリをモーダル経由で選択
        print("介護カテゴリを選択中...")
        try:
            # ラベルクリックでモーダルを開く
            await page.click('label[for="ID_daiEasyShokusyuBox13"]')
            await page.wait_for_timeout(1500)

            # モーダルのHTML構造を確認（デバッグ用）
            modal_html = await page.evaluate("""
                () => {
                    const modal = document.querySelector('.modal_wrap.mom');
                    return modal ? modal.innerHTML.substring(0, 2000) : 'モーダルなし';
                }
            """)
            print(f"モーダルHTML先頭: {modal_html[:800]}")

            # モーダルを閉じる（一旦Escapeで）
            await page.keyboard.press('Escape')
            await page.wait_for_timeout(500)
        except Exception as e:
            print(f"介護チェックボックスエラー: {e}")

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
