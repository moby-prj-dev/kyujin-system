"""
郵送営業一式(送付状バッチ + 宛名ラベル)を生成する。

使い方:
  python3 src/make_mailing.py <宛先JSON> <送付日 例:2026年10月6日> <出力名の接尾辞 例:介護施設97件_20261006>

宛先JSON: [{"zip": "〒901-2131", "addr": "沖縄県...", "name": "合同会社 ユウガ ..."}, ...]
出力: 送付状バッチ_<接尾辞>.pdf / 宛名ラベル_<接尾辞>.pdf (このスクリプトの1つ上のフォルダ)

宛名ラベルは A-one 72312 相当(A4 12面・2列×6段)。元の宛名ラベルPDFと同じ座標に配置する。
"""
import html
import json
import re
import sys
from pathlib import Path

from playwright.sync_api import sync_playwright

BASE = Path(__file__).resolve().parent.parent

SENDER = {
    'org':   'Care Entry（ケアエントリー） 運営',
    'name':  '沖縄デジタルワークス 代表 岸本 安史',
    'addr':  '〒901-0225 沖縄県豊見城市渡橋名160 県営渡橋名団地2-202',
    'tel':   'TEL：070-6401-9492（平日 10:00〜18:00）',
    'email': 'Email：careentry.info@gmail.com',
    'url':   'URL：https://care-entry.net',
}

BODY = [
    '拝啓 時下ますますご清栄のこととお慶び申し上げます。',
    'このたびは、貴施設様の介護職員・看護職員の採用の一助になればと考え、沖縄県内の介護・福祉分野に特化した求人サイト「Care Entry（ケアエントリー）」のサービス紹介資料を送付させていただきます。',
    '当サービスは「応募がなければ掲載料は0円」の成果報酬型で、ベーシックプランなら初期費用・月額費用ともに0円でご利用いただけます。採用が決定した場合の成功報酬もいただきません。',
    'また、求職者は普段お使いのLINEからも応募できるほか、応募前に希望条件を確認する仕組みと、事業所様が独自に設定できる選考質問により、条件の合わない応募を減らす設計となっております。',
    'ご多用のところ大変恐縮ではございますが、同封の資料をご一読いただけますと幸いです。',
    '何卒よろしくお願い申し上げます。',
]


def clean_name(name: str) -> str:
    """元データの崩れを直す: 文字としての '\\n'・抽出時の折返し・全角英数・全角ハイフン・末尾の御中"""
    name = name.replace('\\n', '').replace('\n', '')
    name = re.sub(r'\s*御中\s*$', '', name)
    # 見た目が英字と同じキリル文字(М・А・Е 等)が混入していることがあるため英字に置換
    name = name.translate(str.maketrans('АВЕКМНОРСТХаеорсх', 'ABEKMHOPCTXaeopcx'))
    # 全角英数字 → 半角
    name = re.sub(r'[Ａ-Ｚａ-ｚ０-９]', lambda m: chr(ord(m.group()) - 0xFEE0), name)
    # カタカナの間の全角ハイフン(－)は長音(ー)
    name = re.sub(r'(?<=[ァ-ヶ])－(?=[ァ-ヶ])', 'ー', name)
    name = re.sub(r'[ 　]+', '　', name).strip('　 ')
    return name


def esc(s: str) -> str:
    return html.escape(s)


FONT = "<link href=\"https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap\" rel=\"stylesheet\">"


def cover_html(names, date):
    pages = []
    for n in names:
        pages.append(f"""
<section class="page">
  <p class="date">{esc(date)}</p>
  <p class="to">{esc(n)}<br>御中</p>
  <div class="from">
    {'<br>'.join(esc(v) for v in SENDER.values())}
  </div>
  <h1>沖縄の介護・福祉求人サイトのご案内（資料送付のお願い）</h1>
  {''.join(f'<p class="body">{esc(b)}</p>' for b in BODY)}
  <p class="keigu">敬具</p>
  <p class="ki">記</p>
  <p class="enc"><span>同封物</span>サービス紹介資料 …… 1部</p>
  <p class="ijo">以上</p>
</section>""")
    return f"""<!doctype html><html lang="ja"><head><meta charset="utf-8">{FONT}<style>
@page {{ size: A4; margin: 0; }}
* {{ box-sizing: border-box; }}
body {{ margin: 0; font-family: 'Noto Sans JP', 'IPAexGothic', sans-serif; color: #000; }}
.page {{ width: 210mm; height: 297mm; padding: 22mm 22mm 20mm 26mm; page-break-after: always; font-size: 10.5pt; line-height: 1.85; }}
.page:last-child {{ page-break-after: auto; }}
.date {{ text-align: right; margin: 0 0 14mm; }}
.to {{ font-size: 12.5pt; font-weight: 500; margin: 0 0 6mm; line-height: 1.6; max-width: 140mm; }}
.from {{ text-align: right; font-size: 8.8pt; line-height: 1.6; margin-bottom: 10mm; }}
h1 {{ font-size: 13pt; font-weight: 500; text-align: center; margin: 0 0 6mm; padding-bottom: 5mm; border-bottom: 1px solid #000; }}
.body {{ margin: 0 0 3.2mm; }}
.keigu {{ text-align: right; margin: 5mm 0 10mm; }}
.ki {{ text-align: center; margin: 0 0 6mm; }}
.enc {{ margin: 0 0 6mm 4mm; }}
.enc span {{ display: inline-block; width: 34mm; }}
.ijo {{ text-align: right; margin: 0; }}
</style></head><body>{''.join(pages)}</body></html>"""


# 元の宛名ラベルPDFの座標(pt): 列x / 段y(郵便番号の上端)
COL_X = [70, 308]
ROW_Y = [72, 192, 312, 432, 552, 672]
LABEL_TEXT_W = 222  # pt: 隣の列(308pt)にはみ出さない幅


def label_html(recs):
    per_page = len(COL_X) * len(ROW_Y)
    pages = []
    for start in range(0, len(recs), per_page):
        cells = []
        for i, r in enumerate(recs[start:start + per_page]):
            row, col = divmod(i, len(COL_X))
            cells.append(f"""<div class="lb" style="left:{COL_X[col]}pt;top:{ROW_Y[row]}pt">
  <p class="zip">{esc(r['zip'])}</p>
  <p class="addr">{esc(r['addr'])}</p>
  <p class="nm">{esc(r['clean_name'])}　御中</p>
</div>""")
        pages.append(f'<section class="page">{"".join(cells)}</section>')
    return f"""<!doctype html><html lang="ja"><head><meta charset="utf-8">{FONT}<style>
@page {{ size: A4; margin: 0; }}
body {{ margin: 0; font-family: 'Noto Sans JP', 'IPAexGothic', sans-serif; color: #000; }}
.page {{ position: relative; width: 595.28pt; height: 841.89pt; page-break-after: always; overflow: hidden; }}
.page:last-child {{ page-break-after: auto; }}
.lb {{ position: absolute; width: {LABEL_TEXT_W}pt; }}
.lb p {{ margin: 0; }}
.zip {{ font-size: 10pt; line-height: 1.2; }}
.addr {{ font-size: 7.6pt; line-height: 1.35; margin-top: 15pt !important; }}
.nm {{ font-size: 9.6pt; line-height: 1.4; margin-top: 8pt !important; }}
</style></head><body>{''.join(pages)}</body></html>"""


def render(html_str: str, out: Path):
    tmp = out.with_suffix('.tmp.html')
    tmp.write_text(html_str, encoding='utf-8')
    with sync_playwright() as p:
        b = p.chromium.launch()
        pg = b.new_page()
        pg.goto(tmp.as_uri(), wait_until='networkidle')
        pg.evaluate('document.fonts.ready')
        pg.pdf(path=str(out), format='A4', print_background=True, prefer_css_page_size=True)
        b.close()
    tmp.unlink()


def main():
    src, date, suffix = sys.argv[1], sys.argv[2], sys.argv[3]
    recs = json.loads(Path(src).read_text(encoding='utf-8'))
    for r in recs:
        r['clean_name'] = clean_name(r['name'])
    render(cover_html([r['clean_name'] for r in recs], date), BASE / f'送付状バッチ_{suffix}.pdf')
    render(label_html(recs), BASE / f'宛名ラベル_{suffix}.pdf')
    print(f'{len(recs)}件: 送付状バッチ_{suffix}.pdf / 宛名ラベル_{suffix}.pdf')


if __name__ == '__main__':
    main()
