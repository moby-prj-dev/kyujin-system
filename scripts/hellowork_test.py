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

        print(f"タイトル: {await page.title()}")

        # 介護カテゴリのチェックボックスをチェック（value=13）
        print("介護カテゴリを選択中...")
        try:
            await page.check('input[name="daiEasyShokusyuBox"][value="13"]')
            await page.wait_for_timeout(500)
        except Exception as e:
            print(f"介護チェックボックスエラー: {e}")

        # 都道府県（沖縄）を入力
        print("都道府県（沖縄）を設定中...")
        try:
            await page.fill('input[id="ID_todohukenHidden"]', '47')
        except Exception as e:
            print(f"都道府県設定エラー: {e}")

        # 検索ボタンをクリック
        print("検索実行中...")
        try:
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
