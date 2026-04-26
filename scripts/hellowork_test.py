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

        # ページロード時に出現するmomオーバーレイを非表示
        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")

        # 介護・福祉カテゴリを直接チェック
        # モーダル内の easyShokusyuBox チェックボックスはフォームに直接埋め込まれているため
        # モーダル操作不要でそのまま設定できる
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

        # 都道府県（沖縄=47）を設定
        print("都道府県（沖縄）を設定中...")
        await page.evaluate("""
            () => {
                const hidden = document.getElementById('ID_todohukenHidden');
                if (hidden) hidden.value = '47';
            }
        """)
        await page.wait_for_timeout(300)

        # momオーバーレイを再度非表示にしてから検索ボタンをクリック
        print("検索実行中...")
        await page.evaluate("() => { document.querySelectorAll('.mom').forEach(el => el.style.display = 'none'); }")
        await page.click('button[name="searchBtn"]')
        await page.wait_for_timeout(3000)

        print(f"結果ページタイトル: {await page.title()}")
        print(f"URL: {page.url}")

        # 結果ページHTML構造を確認（求人リスト部分）
        page_html = await page.content()
        # 求人リストは概ねHTML中盤以降にある
        midpoint = len(page_html) // 3
        print("=== 結果HTML抜粋（中盤〜） ===")
        print(page_html[midpoint:midpoint + 3000])

if __name__ == "__main__":
    asyncio.run(scrape_hellowork())
