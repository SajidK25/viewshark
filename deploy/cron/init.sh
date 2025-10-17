#!/bin/sh
set -e

# Write cron cfgs for chat-server and vod-server based on env
CHAT_CFG="/srv/easystream/f_modules/m_frontend/m_cron/chat-server/cfg.php"
VOD_CFG="/srv/easystream/f_modules/m_frontend/m_cron/vod-server/cfg.php"

mkdir -p "$(dirname "$CHAT_CFG")" "$(dirname "$VOD_CFG")"

cat > "$CHAT_CFG" <<'EOF'
<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* database */
$dbhost = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'easystream';
$dbuser = getenv('DB_USER') ?: 'easystream';
$dbpass = getenv('DB_PASS') ?: 'easystream';
/* main url */
$base = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
$ssk = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
EOF

cat > "$VOD_CFG" <<'EOF'
<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* path to recordings */
$path = getenv('VOD_REC_PATH') ?: '/mnt/rec';
/* main url */
$base = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
$ssk = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
EOF

# Ensure f_data is writable (best effort, may be a bind mount)
chown -R 33:33 /srv/easystream/f_data 2>/dev/null || true
chmod -R g+rwX /srv/easystream/f_data 2>/dev/null || true

# Load crontab and start cron in foreground
crontab /etc/cron.d/easystream
cron -f
