FFmpeg ABR Profiles (Examples)

This project ships an example ABR pipeline in `deploy/abr.sh`. Below are suggested profiles and flags you can adapt.

Targets (H.264 + AAC, 48 fps GOP=48):

- 360p: 800 kbps (maxrate 856k, bufsize 1200k)
- 480p: 1400 kbps (maxrate 1498k, bufsize 2100k)
- 720p: 2800 kbps (maxrate 2996k, bufsize 4200k)

Recommended flags:

- Video: `-c:v libx264 -preset veryfast -g 48 -sc_threshold 0 -filter:v "scale=w=-2:h=<height>"`
- Audio: `-c:a aac -b:a 128k -ac 2`
- HLS: `-f hls -hls_time 4 -hls_playlist_type event -hls_flags independent_segments`
- Var streams: `-var_stream_map 'v:0,a:0 v:1,a:1 v:2,a:2'`
- Output pattern: `-master_pl_name master.m3u8 -hls_segment_filename /var/www/hls/abr/${ABR_STREAM_KEY}/%v/seg_%06d.ts /var/www/hls/abr/${ABR_STREAM_KEY}/%v/index.m3u8`

Usage:

- Set `ABR_STREAM_KEY` to the incoming RTMP stream key used by SRS.
- Start the ABR container (already included in docker-compose) to generate `/hls/abr/<key>/{0,1,2}/index.m3u8` and `master.m3u8`.

SRS-based Transcoding (Optional)

SRS supports an internal `transcode` directive. If you prefer SRS to run FFmpeg for you, add a `transcode` block to `deploy/srs.conf` mapping the input app/stream to multi-rendition outputs, and write resulting HLS to the same `/srs/hls` volume.

