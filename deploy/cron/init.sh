#!/bin/sh
set -e

# Write cron cfgs for chat-server and vod-server based on env
CHAT_CFG="/srv/viewshark/f_modules/m_frontend/m_cron/chat-server/cfg.php"
VOD_CFG="/srv/viewshark/f_modules/m_frontend/m_cron/vod-server/cfg.php"

mkdir -p "." "."

cat > "" <<EOF
<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* database */
\ = getenv('DB_HOST') ?: 'db';
\ = getenv('DB_NAME') ?: 'viewshark';
\ = getenv('DB_USER') ?: 'viewshark';
\ = getenv('DB_PASS') ?: 'viewshark';
/* main url */
\ = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
\ = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
EOF

cat > "" <<EOF
<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* path to recordings */
\ = getenv('VOD_REC_PATH') ?: '/mnt/rec';
/* main url */
\ = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
\ = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
EOF

# Ensure f_data is writable (best effort, may be a bind mount)
chown -R 33:33 /srv/viewshark/f_data 2>/dev/null || true
chmod -R g+rwX /srv/viewshark/f_data 2>/dev/null || true

# Load crontab and start cron in foreground
crontab /etc/cron.d/viewshark
cron -f