-- Initialize key settings for local testing
UPDATE db_settings SET cfg_data='1' WHERE cfg_name='live_module';
UPDATE db_settings SET cfg_data='https://test.watchmaji.com/hls' WHERE cfg_name='live_hls_server';