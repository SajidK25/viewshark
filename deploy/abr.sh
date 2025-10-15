#!/bin/bash

set -e  # Exit on any error for debugging

# Create directories for ABR variants (0=360p, 1=480p, 2=720p)
mkdir -p "/var/www/hls/abr/${ABR_STREAM_KEY}/0"
mkdir -p "/var/www/hls/abr/${ABR_STREAM_KEY}/1"
mkdir -p "/var/www/hls/abr/${ABR_STREAM_KEY}/2"

echo "[ABR] Waiting for RTMP stream key '${ABR_STREAM_KEY}'..."

while true; do
  ffmpeg -hide_banner -loglevel warning \
    -i "rtmp://srs/live/${ABR_STREAM_KEY}" \
    -filter:v:0 "scale=w=-2:h=360" -c:v:0 libx264 -b:v:0 800k -maxrate:v:0 856k -bufsize:v:0 1200k -preset veryfast -g 48 -sc_threshold 0 \
    -filter:v:1 "scale=w=-2:h=480" -c:v:1 libx264 -b:v:1 1400k -maxrate:v:1 1498k -bufsize:v:1 2100k -preset veryfast -g 48 -sc_threshold 0 \
    -filter:v:2 "scale=w=-2:h=720" -c:v:2 libx264 -b:v:2 2800k -maxrate:v:2 2996k -bufsize:v:2 4200k -preset veryfast -g 48 -sc_threshold 0 \
    -map a:0 -c:a aac -b:a:0 128k -ac 2 \
    -map a:0 -c:a aac -b:a:1 128k -ac 2 \
    -map a:0 -c:a aac -b:a:2 128k -ac 2 \
    -map v:0 -map v:1 -map v:2 -var_stream_map 'v:0,a:0 v:1,a:1 v:2,a:2' \
    -f hls -hls_time 4 -hls_playlist_type event -hls_flags independent_segments \
    -master_pl_name master.m3u8 \
    -hls_segment_filename "/var/www/hls/abr/${ABR_STREAM_KEY}/%v/seg_%06d.ts" \
    "/var/www/hls/abr/${ABR_STREAM_KEY}/%v/index.m3u8" || true

  echo "[ABR] ffmpeg exited. Retrying in 5s..."
  sleep 5
done