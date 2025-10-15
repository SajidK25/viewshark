<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty language plugin
 *
 * Type:     function<br>
 * Name:     menu_panel<br>
 * Purpose:  generate html<br>
 * @author n/a
 * @param array
 */
function smarty_function_generate_html($params, &$smarty) {
    global $class_filter;

    switch($params['type']) {
	case "homepage"://homepage
		return VHome::doLayout();
	break;
	case "my_channels_layout"://manage my channels
		return VChannel::manageLayout();
	break;
	case "channel_layout"://channel page
		return VChannel::channelLayout();
	break;
	case "channels_layout"://browse channels
		return VChannels::doLayout();
	break;
	case "file_options"://file config options
		return VUpload::fileSettings($params['type']);
	case "contacts_layout"://contacts
		return VContacts::contactsLayout();
	break;
	case "vjs_ads": //videojs ads
	case "jw_ads": //jwplayer ads
        case "jw_files": //jwplayer files
        case "fp_ads": //flowplayer ads
	case "pk_entry": //packages
	case "sub_entry": //subscription types
	case "dc_entry": //discount codes
	case "pmsg_entry": //priv msg
	case "ch_types": //channel types
	case "ch_categ"://public categories
	case "ban_list"://ban list
	case "lang_types"://ban list
	case "adv_groups"://ad groups
	case "adv_banners"://banner ads
	case "be_servers"://upload servers
        case "be_xfer_video"://video transfers
        case "be_xfer_short"://short transfers
        case "be_xfer_image"://image transfers
        case "be_xfer_audio"://audio transfers
        case "be_xfer_doc"://doc transfers
        case "be_live_streaming"://doc transfers
        case "be_live_streaming_token"://token
        case "be_token_purchase"://token purchases
        case "be_token_donation"://token donations
        case "be_file_comments"://video transfers
	    $bullet_id 		= $params['bullet_id'];
	    $entry_id		= $params['entry_id'];
	    $bottom_border	= $params['bb'];

	    return VbeEntries::listEntries($params['type'], $bullet_id, $entry_id, $bottom_border);
	break;
	case "user_accounts": //backend account management
	    return VbeMembers::accountManager();
	break;
	case "file_manager_live"://backend files management - broadcasts
	    return VbeFiles::fileManager('live');
	break;
	case "file_manager_video"://backend files management - videos
	    return VbeFiles::fileManager('video');
	break;
	case "file_manager_short"://backend files management - shorts
	    return VbeFiles::fileManager('short');
	break;
	case "file_manager_image"://backend files management - images
	    return VbeFiles::fileManager('image');
	break;
	case "file_manager_audio"://backend files management - audios
	    return VbeFiles::fileManager('audio');
	break;
	case "file_manager_doc"://backend files management - documents
	    return VbeFiles::fileManager('doc');
	break;
	case "file_manager_blog"://backend files management - blogs
	    return VbeFiles::fileManager('blog');
	break;
	case "user-type-actions-be"://backend user sort menu
	    return VbeMembers::listMenuActions($params['type']);
	break;
	case "file-time-actions-be"://backend file sort menu
	    return VbeFiles::listMenuActions($params['type']);
	break;
	case "file-type-actions-be"://backend type sort menu
	    return VbeFiles::listTypeActions($params['type']);
	break;
	case "search_layout"://search results
	    return VSearch::searchLayout();
	break;
	case "comments_layout"://comments
	    return VComments::seeAllComments();
	break;
	case "responses_layout"://see responses
	    return VResponses::seeAllResponses();
	break;
	case "file_responses_layout"://file responses
	    return VFiles::CR_tophtml('responses');
	break;
	case "file_comments_layout"://file comments
	    return VFiles::CR_tophtml('comments');
	break;
	case "response_layout"://responses
	    return VResponses::responseLayout();
	break;
	case "browse_layout"://browse files
	    return VBrowse::browseLayout();
	break;
	case "view_layout"://view files
	    return VView::viewLayout();
	break;
	case "files_layout"://own files
	    return VFiles::browseLayout();
	break;
	case "playlist_layout"://my playlists
	    return VFiles::listPlaylists();
	break;
	case "playlists_layout"://browse playlists
	    return VPlaylist::doLayout();
	break;
	case "playlist_details_layout"://playlist details
	    return VFiles::listPlaylistDetails();
	break;
	case "files_edit_layout"://edit files
	    return VFiles::fileEdit();
	break;
	case "file-type-actions"://file type menu
	case "file-time-actions"://file sort menu
	    return VFiles::listMenuActions($params['type']);
	break;
	case "playlist-sort-options":
	    return VFiles::sortPlaylists();
	break;
	case "commresp-sort-options":
	    return VFiles::sortComments();
	break;
	case "playlist-cfg-tabs"://playlist config tabs
	    return VFiles::plCfgTabs();
	break;

	default://backend settings
	    $bullet_id 		= $params['bullet_id'];
	    $input_type		= $params['input_type'];
	    $entry_title	= $params['entry_title'];
	    $col_type		= $params['col_type'];
	    $entry_id		= $params['entry_id'];
	    $input_name		= $params['input_name'];
	    $input_value	= $params['input_value'];
	    $bottom_border	= $params['bb'];
	    $section		= $params['section'];

	    return VbeSettings::div_setting_input($bullet_id,$input_type,$entry_title,$entry_id,$input_name,$input_value,$bottom_border,$section,$col_type);
	break;
    }
}
?>