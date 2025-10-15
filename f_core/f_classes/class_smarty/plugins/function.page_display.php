<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty page plugin
 *
 * Type:     function<br>
 * Name:     page_display<br>
 * Purpose:  display a section<br>
 * @author:  n/a
 * @param array
 */
function smarty_function_page_display($params, &$smarty) { 
    global $smarty;
    $section 	= $params['section'];
    $menu 	= $params['menu'];

    switch($menu){
	case 'tpl_account':		$smarty->display('tpl_frontend/tpl_leftnav/tpl_nav_account.tpl'); break;
	case 'tpl_messages':		$smarty->display('tpl_frontend/tpl_leftnav/tpl_nav_messages.tpl'); break;
	case 'tpl_files':		$smarty->display('tpl_frontend/tpl_leftnav/tpl_nav_files.tpl'); break;
	case 'tpl_subs':		$smarty->display('tpl_frontend/tpl_leftnav/tpl_nav_subs.tpl'); break;
	case 'tpl_browse':		$smarty->display('tpl_frontend/tpl_leftnav/tpl_nav_categs.tpl'); break;
    }

    switch($section){
	//for frontend
	case 'tpl_error':		$smarty->display('tpl_frontend/tpl_error.tpl'); break;
	case 'tpl_index':		$smarty->display('tpl_frontend/tpl_index.tpl'); break;
	case 'tpl_signin':		$smarty->display('tpl_frontend/tpl_auth/tpl_signin.tpl'); break;
	case 'tpl_signup':		$smarty->display('tpl_frontend/tpl_auth/tpl_signup.tpl'); break;
	case 'tpl_welcome':		$smarty->display('tpl_frontend/tpl_auth/tpl_welcome.tpl'); break;
	case 'tpl_payment':		$smarty->display('tpl_frontend/tpl_auth/tpl_payment.tpl'); break;
	case 'tpl_recovery':		$smarty->display('tpl_frontend/tpl_auth/tpl_recovery.tpl'); break;
	case 'tpl_verify':		$smarty->display('tpl_frontend/tpl_acct/tpl_verify.tpl'); break;
	case 'tpl_account':		$smarty->display('tpl_frontend/tpl_acct/tpl_overview.tpl'); break;
	case 'tpl_affiliate':		$smarty->display('tpl_frontend/tpl_affiliate/tpl_overview.tpl'); break;
	case 'tpl_subscribers':		$smarty->display('tpl_frontend/tpl_subscriber/tpl_subscribers.tpl'); break;
	case 'tpl_tokens':              $smarty->display('tpl_frontend/tpl_token/tpl_tokens.tpl'); break;
	case 'tpl_channel':		$smarty->display('tpl_frontend/tpl_acct/tpl_channel.tpl'); break;
	case 'tpl_manage_channel':	$smarty->display('tpl_frontend/tpl_acct/tpl_manage_channel.tpl'); break;
	case 'tpl_channels':		$smarty->display('tpl_frontend/tpl_acct/tpl_channels.tpl'); break;
	case 'tpl_messages':		$smarty->display('tpl_frontend/tpl_msg/tpl_messages.tpl'); break;
	case 'tpl_friend_action':	$smarty->display('tpl_frontend/tpl_msg/tpl_friend_action.tpl'); break;
	case 'tpl_upload':		$smarty->display('tpl_frontend/tpl_file/tpl_upload.tpl'); break;
	case 'tpl_files':		$smarty->display('tpl_frontend/tpl_file/tpl_files.tpl'); break;
	case 'tpl_subs':		$smarty->display('tpl_frontend/tpl_file/tpl_files.tpl'); break;
	case 'tpl_files_edit':		$smarty->display('tpl_frontend/tpl_file/tpl_files_edit.tpl'); break;
	case 'tpl_playlist':		$smarty->display('tpl_frontend/tpl_file/tpl_playlist.tpl'); break;
	case 'tpl_playlists':		$smarty->display('tpl_frontend/tpl_file/tpl_playlists.tpl'); break;
	case 'tpl_blogs':		$smarty->display('tpl_frontend/tpl_file/tpl_blogs.tpl'); break;
	case 'tpl_browse':		$smarty->display('tpl_frontend/tpl_file/tpl_browse.tpl'); break;
	case 'tpl_view':		$smarty->display('tpl_frontend/tpl_file/tpl_view.tpl'); break;
	case 'tpl_comments':		$smarty->display('tpl_frontend/tpl_file/tpl_view_extra.tpl'); break;
	case 'tpl_respond':		$smarty->display('tpl_frontend/tpl_file/tpl_respond.tpl'); break;
	case 'tpl_respond_extra':	$smarty->display('tpl_frontend/tpl_file/tpl_respond_extra.tpl'); break;
	case 'tpl_search':		$smarty->display('tpl_frontend/tpl_file/tpl_search_main.tpl'); break;
	case 'tpl_page':		return VGenerate::pageHTML(); break;
	//for backend
	case 'backend_tpl_signin':	$smarty->display('tpl_backend/tpl_signin.tpl'); break;
	case 'backend_tpl_recovery':	$smarty->display('tpl_backend/tpl_auth/tpl_recovery.tpl'); break;
	case 'backend_tpl_main':	$smarty->display('tpl_backend/tpl_main.tpl'); break;
	case 'backend_tpl_settings':	$smarty->display('tpl_backend/tpl_settings.tpl'); break;
	case 'backend_tpl_dashboard':	$smarty->display('tpl_backend/tpl_dashboard.tpl'); break;
	case 'backend_tpl_analytics':	$smarty->display('tpl_backend/tpl_analytics.tpl'); break;
	case 'backend_tpl_affiliate':	$smarty->display('tpl_backend/tpl_affiliate.tpl'); break;
	case 'backend_tpl_subscriber':	$smarty->display('tpl_backend/tpl_subscriber/tpl_subscribers.tpl'); break;
	case 'backend_tpl_token':       $smarty->display('tpl_backend/tpl_token/tpl_tokens.tpl'); break;
	case 'backend_tpl_members':	$smarty->display('tpl_backend/tpl_members.tpl'); break;
	case 'backend_tpl_files':	$smarty->display('tpl_backend/tpl_files.tpl'); break;
	case 'backend_tpl_advertising':	$smarty->display('tpl_backend/tpl_advertising.tpl'); break;
	case 'backend_tpl_upload':	$smarty->display('tpl_backend/tpl_upload.tpl'); break;
	case 'backend_tpl_players':	$smarty->display('tpl_backend/tpl_players.tpl'); break;
	case 'backend_tpl_import':
        case 'tpl_import':
        	return VImport::videoFeeds_layout();
        	
        	break;
    }
}
?>