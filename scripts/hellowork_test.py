"""
ハローワーク スクレイピングテスト（requests + 標準ライブラリ版）
"""
import requests
from html.parser import HTMLParser

headers = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
}

url = "https://www.hellowork.mhlw.go.jp/member/kplist?screenId=&action=search&pref=47&city=&keyword=&job=13&category=&employment=&search=1"

print(f"アクセス中: {url}")
try:
    response = requests.get(url, headers=headers, timeout=15)
    print(f"ステータス: {response.status_code}")
    print(f"Content-Type: {response.headers.get('Content-Type', '')}")
    print(f"HTML長さ: {len(response.text)}")
    print("\n--- HTMLの先頭1000文字 ---")
    print(response.text[:1000])
except Exception as e:
    print(f"エラー: {e}")
