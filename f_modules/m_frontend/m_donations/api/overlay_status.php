<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2024 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

define('_ISVALID', true);

include_once '../../../f_core/config.core.php';

header('Content-Type: application/json');

$usr_key = $class_filter->clr_str($_GET['u']);
if ($usr_key == '') { echo json_encode(['recent'=>[], 'goal'=>['title'=>'','raised'=>0,'target'=>0]]); exit; }

// map usr_key -> usr_id
$usr_id = (int) $class_database->singleFieldValue('db_accountuser','usr_id','usr_key',$usr_key);

$recent = [];
$goal   = ['title'=>'','raised'=>0,'target'=>0];

try {
    // Donations may be stored by the donations module; try common table names
    // recent donations
    $q = $db->execute(sprintf("SELECT `donor_name`,`amount`,`message`,`created_at` FROM `donations` WHERE `channel_usr_id`='%s' ORDER BY `created_at` DESC LIMIT 5;", $usr_id));
    while(!$q->EOF){
        $recent[] = [
            'name' => $q->fields['donor_name'],
            'amount' => (float) $q->fields['amount'],
            'message' => $q->fields['message'],
            'time' => $q->fields['created_at'],
        ];
        $q->MoveNext();
    }
} catch (Exception $e) { /* table missing -> ignore */ }

try {
    $g = $db->execute(sprintf("SELECT `title`,`target_amount`,`current_amount` FROM `donation_goals` WHERE `channel_usr_id`='%s' AND `active`='1' ORDER BY `id` DESC LIMIT 1;", $usr_id));
    if ($g->fields['title'] != ''){
        $goal['title']  = $g->fields['title'];
        $goal['target'] = (float) $g->fields['target_amount'];
        $goal['raised'] = (float) $g->fields['current_amount'];
    }
} catch (Exception $e) { /* ignore */ }

echo json_encode(['recent'=>$recent, 'goal'=>$goal]);

