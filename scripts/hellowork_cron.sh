#!/bin/bash
# ハローワーク求人データ 毎日自動取得スクリプト(JSONのみ更新)
# crontabに追加: 0 3 * * * /bin/bash /home/moby0619/kyujin-system/scripts/hellowork_cron.sh
#
# JSON → DB への取り込みは Laravel スケジューラ側で
# hellowork:generate-lps が週次で「未登録のうち上位N件」を追加する。
# (このcronで全件import すると週次累積モデルが崩れる)

set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
LOG_FILE="$SCRIPT_DIR/hellowork_cron.log"
VENV_PYTHON="/home/moby0619/playwright-env/bin/python"
PROJECT_DIR="$SCRIPT_DIR/.."

echo "[$(date '+%Y-%m-%d %H:%M:%S')] === スクレイピング開始 ===" >> "$LOG_FILE"

cd "$PROJECT_DIR"

"$VENV_PYTHON" "$SCRIPT_DIR/hellowork_test.py" >> "$LOG_FILE" 2>&1

echo "[$(date '+%Y-%m-%d %H:%M:%S')] === スクレイピング完了 (JSONのみ更新) ===" >> "$LOG_FILE"
