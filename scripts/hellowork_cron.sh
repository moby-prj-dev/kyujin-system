#!/bin/bash
# ハローワーク求人データ 毎日自動取得スクリプト
# crontabに追加: 0 3 * * * /bin/bash /home/moby0619/kyujin-system/scripts/hellowork_cron.sh

set -e

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
LOG_FILE="$SCRIPT_DIR/hellowork_cron.log"
VENV_PYTHON="/home/moby0619/playwright-env/bin/python"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] スクレイピング開始" >> "$LOG_FILE"

cd "$SCRIPT_DIR/.."

"$VENV_PYTHON" "$SCRIPT_DIR/hellowork_test.py" >> "$LOG_FILE" 2>&1

echo "[$(date '+%Y-%m-%d %H:%M:%S')] 完了" >> "$LOG_FILE"
