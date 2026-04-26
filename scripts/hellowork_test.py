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

        # 介護カテゴリのチェックボックスをチェック（value=13）
        # labelクリックはモーダルを開くのでJSで直接チェック
        print("介護カテゴリを選択中...")
        try:
            await page.evaluate("""
                () => {
                    const cb = document.querySelector('input[name="daiEasyShokusyuBox"][value="13"]');
                    if (cb) cb.checked = true;
                }
            """)
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
        print("\n--- 結果テキスト（先頭500文字）---")
        print(content[:500])

        await browser.close()

if __name__ == "__main__":
    asyncio.run(scrape_hellowork())
