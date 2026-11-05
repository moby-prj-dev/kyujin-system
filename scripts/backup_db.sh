#!/bin/bash
# 本番DBの日次バックアップ(mysqldump → gzip)。14日より古いものは削除。
# crontab: 0 5 * * * /bin/bash /home/moby0619/kyujin-system/scripts/backup_db.sh
#
# 復元例:
#   gunzip -c ~/backups/db/care_entry_YYYYMMDD_HHMM.sql.gz | \
#     docker compose exec -T mysql sh -c 'exec mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"'

set -uo pipefail

PROJECT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
BACKUP_DIR="$HOME/backups/db"
KEEP_DAYS=14
LOG="$BACKUP_DIR/backup.log"

mkdir -p "$BACKUP_DIR"
cd "$PROJECT_DIR" || exit 1

TS=$(date +%Y%m%d_%H%M)
OUT="$BACKUP_DIR/care_entry_${TS}.sql.gz"
TMP="$OUT.tmp"

# --single-transaction: InnoDBをロックせず整合性のあるダンプを取る
docker compose exec -T mysql sh -c \
    'exec mysqldump -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" --single-transaction --no-tablespaces --routines "$MYSQL_DATABASE"' \
    2>/dev/null | gzip > "$TMP"
STATUS=("${PIPESTATUS[@]}")

# mysqldump が正常終了し、末尾の完了マーカーがあることを確認してから確定する
if [ "${STATUS[0]}" -ne 0 ] || [ ! -s "$TMP" ] || ! gunzip -c "$TMP" | tail -n 1 | grep -q "Dump completed"; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] FAILED (mysqldump exit=${STATUS[0]})" >> "$LOG"
    rm -f "$TMP"
    exit 1
fi

mv "$TMP" "$OUT"
find "$BACKUP_DIR" -name 'care_entry_*.sql.gz' -mtime +"$KEEP_DAYS" -delete
echo "[$(date '+%Y-%m-%d %H:%M:%S')] OK $(du -h "$OUT" | cut -f1) $(basename "$OUT")" >> "$LOG"
