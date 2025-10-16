<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);

include_once '../../../f_core/config.core.php';

$usr_key = $class_filter->clr_str($_GET['u']);

header('Content-Type: text/html; charset=UTF-8');
?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Donations Overlay</title>
  <style>
    html,body{margin:0;height:100%;background:transparent;overflow:hidden}
    .ov-wrap{position:relative;width:100%;height:100%;font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#fff}
    .event{position:absolute;left:16px;bottom:16px;background:rgba(0,0,0,.55);padding:12px 14px;border-radius:8px;backdrop-filter:saturate(150%) blur(6px);box-shadow:0 6px 18px rgba(0,0,0,.3)}
    .event .name{font-weight:600}
    .event .amount{color:#6cf;margin-left:8px}
    .goal{position:absolute;right:16px;top:16px;background:rgba(0,0,0,.55);padding:10px 12px;border-radius:8px}
    .bar{width:220px;height:8px;background:rgba(255,255,255,.2);border-radius:6px;margin-top:8px;overflow:hidden}
    .fill{height:100%;background:#6cf;width:0%}
  </style>
  <script>const uKey = <?php echo json_encode($usr_key); ?>;</script>
</head>
<body>
  <div class="ov-wrap">
    <div class="goal">
      <div id="goal-title">Goal</div>
      <div class="bar"><div id="goal-fill" class="fill"></div></div>
    </div>
    <div id="event" class="event" style="display:none"></div>
  </div>
  <script>
    async function tick(){
      try{
        const res = await fetch('../api/overlay_status.php?u='+encodeURIComponent(uKey), {cache:'no-store'});
        const j = await res.json();
        const ev = document.getElementById('event');
        if (j && j.recent && j.recent[0]){
          const r = j.recent[0];
          ev.innerHTML = `<span class="name">${r.name || 'Someone'}</span> <span class="amount">$${Number(r.amount||0).toFixed(2)}</span><div>${r.message?r.message:''}</div>`;
          ev.style.display='block';
        }
        const pct = Math.max(0, Math.min(100, j.goal && j.goal.target>0 ? (j.goal.raised/j.goal.target*100) : 0));
        document.getElementById('goal-fill').style.width = pct+'%';
        document.getElementById('goal-title').textContent = j.goal && j.goal.title ? j.goal.title : 'Goal';
      }catch(e){/* noop */}
    }
    tick(); setInterval(tick, 5000);
  </script>
</body>
/html>

