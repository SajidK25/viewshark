<?php
defined('_ISVALID') or header('Location: /error');

use Akismet\Akismet;
use Akismet\Comment;
use GeoIp2\Database\Reader;
use IPQualityScore\IPQualityScore;

function insert_swiperJS($p)
{return VGenerate::swiperjs($p["for"]);}
function insert_socialMediaLinks()
{return VGenerate::socialMediaLinks();}
function insert_themeSwitch()
{return VGenerate::themeSwitch();}
function insert_loadcssplugins()
{return VGenerate::cssplugins();}
function insert_loadbecssplugins()
{return VGenerate::becssplugins();}
function insert_loadjsplugins()
{return VGenerate::jsplugins();}
function insert_loadbejsplugins()
{return VGenerate::bejsplugins();}
function insert_getSubCount()
{return VHome::getSubCount();}
function insert_getFollowCount()
{return VHome::getSubCount(1);}
function insert_getCurrentSection()
{return VHref::currentSection();}
function insert_getSearchSection()
{return VSearch::getSearchSection();}
function insert_newMessages()
{return VMessages::getnew_nr();}
function insert_viewFileColumn()
{return VView::sideColumn();}
function insert_bePlayerAdCounts($p)
{return VbeAdvertising::playerAdCount($p["for"]);}
function insert_H2span($t)
{return VGenerate::H2span($t["for"], $t["f"]);}
function insert_beUserCount($p)
{return VbeMembers::userCount($p["for"]);}
function insert_beFileCount($p)
{return VbeFiles::fileCount($p["for"]);}
function insert_beSectionList($p)
{return VbeSettings::sectionList($p["for"]);}
function insert_beAdvCount_banner($p)
{return VbeAdvertising::advCount_banner($p["for"]);}
function insert_beAdvCount_grp($p)
{return VbeAdvertising::advCount_group($p["for"]);}
function insert_beMenuToggle()
{return VGenerate::menuToggle();}
function insert_beMenuEntries()
{return VGenerate::menuEntries();}
function insert_advHTML($id)
{return VGenerate::advHTML($id["id"]);}
function insert_getVJSJS($p)
{return VPlayers::VJSJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getJWJS($p)
{return VPlayers::JWJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getFPJS($p)
{return VPlayers::FPJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getDOCJS($p)
{return VPlayers::DOCJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getFLEXJS($p)
{return VPlayers::FLEXJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getFREEJS($p)
{return VPlayers::FREEJS('view', $p["usr_key"], $p["file_key"], $p["file_hd"], $p["next"], $p["pl_key"]);}
function insert_getImageJS($p)
{return VPlayers::imageJS('view', $p["pl_key"]);}
function insert_getPageMeta($for)
{return VHref::getPageMeta($for["for"]);}
function insert_categoryMenu()
{$a = new VBrowse;return VBrowse::categoryMenu();}
function insert_channelsMenu()
{$a = new VChannels;return VChannels::categoryMenu();}
function insert_promotedChannelsMenu()
{$c = new VChannels;return VChannels::promotedMenu();}
function insert_uploadResponse()
{return VResponses::uploadResponse();}
function insert_getUserStats($type)
{return VUseraccount::getUserStats($type["type"]);}
function insert_subsConfig()
{return VFiles::subsConfig();}
function insert_fileListSelect($for)
{return VMessages::fileListSelect($for["for"]);}
function insert_updateSubsCount()
{return VFiles::userSubs(1);}
function insert_updateOsubCount()
{return VFiles::userSubs(2);}
function insert_getUserSubs()
{$a = new VFiles;return VFiles::userSubs();}
function insert_getPlaylistEditID($key)
{return VFiles::userPlaylistEditID($key["key"], $key["get"]);}
function insert_getUserPlaylists($for)
{return VFiles::userPlaylists($for["for"]);}
function insert_getChannelTabs()
{return VUserpage::channelTabs();}
function insert_getChannelLayout()
{return VUserpage::channelLayout();}
function insert_getCustomStyles()
{return VUserpage::customStyles();}
function insert_userLabelCheckboxes()
{return VContacts::labelCheckboxes();}
function insert_sectionLabel($for)
{return VMessages::sectionLabel($for["for"]);}
function insert_addToLabel($for)
{return VMessages::addToLabel($for["for"]);}
function insert_getLabelName()
{return VMessages::getLabelName();}
function insert_fileCount($for)
{return VFiles::fileCount($for["for"]);}
function insert_msgCount($for)
{return VMessages::messageCount($for["for"]);}
function insert_getMessageSubject($msg_id)
{return VMessages::getMessageInfo('msg_subj', $msg_id["msg_id"]);}
function insert_beFileCategories($type)
{return VbeFiles::fileCategories($type["type"]);}
function insert_footerInit()
{return VGenerate::footerInit();}
function insert_langInit()
{return VGenerate::langInit();}
function insert_langInit_be()
{return VGenerate::langInit_be();}
function insert_footerText($p)
{return VGenerate::footerText($p["ct"]);}
function insert_getUserNameKey($key)
{
    global $class_database, $class_filter;
    $k = strlen($key["key"]) == 11 ? substr($key["key"], 1) : $key["key"];
    return $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $class_filter->clr_str($k));
}
function insert_phpInfo()
{
    ob_start();
    phpinfo();
    $i = ob_get_contents();
    ob_end_clean();

    return $i;
}
function insert_currentMenuEntry($for)
{
    return VMessages::currentMenuEntry($for["for"]);
}
function insert_getMessageDate($msg_id)
{
    global $class_database;
    return $for = $_GET["f"] == 'comm' ? $class_database->singleFieldValue('db_channelcomments', 'c_datetime', 'c_id', $msg_id["msg_id"]) : VMessages::getMessageInfo('msg_date', $msg_id["msg_id"]);
}
function insert_getMessageText($msg_id)
{
    global $class_database;
    return '<pre>' . ($_GET["f"] == 'comm' ? $class_database->singleFieldValue('db_channelcomments', 'c_body', 'c_id', $msg_id["msg_id"]) : VMessages::getMessageInfo('msg_body', $msg_id["msg_id"])) . '</pre>';
}
function insert_getUsername($user_id)
{
    $user_details = VUserinfo::getUserInfo($user_id["user_id"]);
    return $user_details["uname"];
}
function insert_getProfileImage($for)
{
    $_for = $for["for"] != '' ? $for["for"] : '';
    return VUseraccount::getProfileImage($_for);
}
function insert_phpInfoText()
{
    global $language;

    return '<span class="">' . $language["backend.menu.entry1.sub7.active"] . '</span> <b>post_max_size ' . ini_get("post_max_size") . '</b> <span class="">' . $language["frontend.global.and"] . '</span> <b>upload_max_filesize ' . ini_get("upload_max_filesize") . '</b>';
}
function insert_generateCountryList()
{
    global $cfg, $language;
    include_once 'f_core/config.countries.php';

    $i        = 0;
    $disabled = $_SESSION["signup_location"] != '' ? 'disabled="disabled"' : null;
    $disabled = $cfg['global_signup'] == 0 ? 'disabled="disabled"' : $disabled;
    $disabled = ($cfg["signup_ip_access"] == 1 and !VIPaccess::checkIPlist($cfg["list_ip_signup"])) ? 'disabled="disabled"' : $disabled;
    $select   = '<select ' . $disabled . ' name="frontend_signup_location_sel" class="signup-select" onChange="$(\'#input-loc\').val(\'' . $language["frontend.signup.location"] . ': \'+this.value);">';

    foreach ($_countries as $value) {
        $selected = $_SESSION["signup_location"] == $value ? ' selected="selected"' : ($_POST["frontend_signup_location"] == $value ? ' selected="selected"' : null);
        $selected = $i == 0 ? ' disabled="disabled" selected="selected"' : $selected;
        $option .= '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
        $i += 1;
    }
    $select .= $option . '</select>';
    return $select;
}
function insert_getListContent($from)
{
    global $class_database, $smarty;
    $cfg = $class_database->getConfigurations('list_signup_terms,list_ip_signup,list_email_domains,list_reserved_users,list_ip_access,list_ip_backend');

    switch ($from["from"]) {
        case 'terms':$url = $cfg["list_signup_terms"];
            break;
        case 'ip-backend':$url = $cfg["list_ip_backend"];
            break;
        case 'ip-access':$url = $cfg["list_ip_access"];
            break;
        case 'ip-signup':$url = $cfg["list_ip_signup"];
            break;
        case 'email-domains':$url = $cfg["list_email_domains"];
            break;
        case 'usernames':$url = $cfg["list_reserved_users"];
            break;
    }
    return $smarty->fetch($url);
}
function insert_arrayFromString($opt)
{
    global $cfg, $language;
    include_once 'f_data/data_languages/' . $_SESSION["fe_lang"] . '/lang_frontend/' . $opt["from"] . '.php';

    $array = explode(',', $language[$opt["entry"]]);
    return $array;
}
function insert_sizeFormat($size)
{
    global $cfg, $language;

    $dlm          = $cfg["numeric_delimiter"];
    $size["size"] = $size["size"] * (1024 * 1024);

    if ($size["size"] < 1024) {return number_format($size["size"], 0, $dlm, $dlm) . $language["frontend.sizeformat.bytes"];} elseif ($size["size"] < (1024 * 1024)) {$size2 = round($size["size"] / 1024, 1);return number_format(($size["size"] / 1024), 0, $dlm, $dlm) . $language["frontend.sizeformat.kb"];} elseif ($size["size"] < (1024 * 1024 * 1024)) {return number_format(($size["size"] / (1024 * 1024)), 0, $dlm, $dlm) . $language["frontend.sizeformat.mb"];} else { $size2 = round($size["size"] / (1024 * 1024 * 1024), 1);return number_format(($size["size"] / (1024 * 1024 * 1024)), 0, $dlm, $dlm) . $language["frontend.sizeformat.gb"];}
}

function secured_encrypt($data)
{
    $method     = "aes-256-cbc";
    $first_key  = base64_decode(ENC_FIRSTKEY);
    $second_key = base64_decode(ENC_SECONDKEY);

    $iv_length        = openssl_cipher_iv_length($method);
    $iv               = openssl_random_pseudo_bytes($iv_length);
    $first_encrypted  = openssl_encrypt($data, $method, $first_key, OPENSSL_RAW_DATA, $iv);
    $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, true);
    $output           = base64_encode($iv . $second_encrypted . $first_encrypted);

    return $output;
}

function secured_decrypt($input)
{
    $method     = "aes-256-cbc";
    $first_key  = base64_decode(ENC_FIRSTKEY);
    $second_key = base64_decode(ENC_SECONDKEY);
    $mix        = base64_decode($input);

    $iv_length            = openssl_cipher_iv_length($method);
    $iv                   = substr($mix, 0, $iv_length);
    $second_encrypted     = substr($mix, $iv_length, 64);
    $first_encrypted      = substr($mix, $iv_length + 64);
    $data                 = openssl_decrypt($first_encrypted, $method, $first_key, OPENSSL_RAW_DATA, $iv);
    $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, true);

    if ($second_encrypted and hash_equals($second_encrypted, $second_encrypted_new)) {
        return $data;
    }

    return false;
}

function check_ip_quality_score($ip)
{
    global $cfg;

    include 'f_core/f_classes/class_ipquality/vendor/autoload.php';

    $key = $cfg['ipqualityscore_api'];

    try {
        $qualityScore = new IPQualityScore($key);
        $result       = $qualityScore->IPAddressVerification
            ->setUserLanguage($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '')
            ->setUserAgent($_SERVER['HTTP_USER_AGENT'] ?? '')
            ->getResponse($ip);

        return $result;
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function akismet_spam_check($c)
{
    global $cfg;

    include 'f_core/f_classes/class_akismet/vendor/autoload.php';

    $key  = $cfg['akismet_api'];
    $site = $cfg['akismet_site'];

    try {
        $akismet = new Akismet($key, $site);
        $comment = new Comment();
        $comment->setContent($c);
        $comment->includeServerVariables();
        $result = $akismet->check($comment);

        if ($result->isSpam) {
            return true;
        }
        if ($result->isDiscardable) {
            return true;
        }
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    return false;
}

function maxmind_country()
{
    global $cfg;

    include 'f_core/f_classes/class_maxmind/vendor/autoload.php';

    try {
        $reader = new Reader($cfg['maxmind_db']);
        $record = $reader->country($_SERVER[REM_ADDR]);

        return $record->country->isoCode;
    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
    }
}

function proxy_check($address)
{
    global $cfg;

    include 'f_core/f_classes/class_proxycheck/vendor/autoload.php';

    try {
        $proxycheck_options = array(
            'API_KEY'           => $cfg['proxycheck_api'], // Your API Key.
            'ASN_DATA'          => 1, // Enable ASN data response.
            'DAY_RESTRICTOR'    => 7, // Restrict checking to proxies seen in the past # of days.
            'VPN_DETECTION'     => 1, // Check for both VPN's and Proxies instead of just Proxies.
            'RISK_DATA'         => 1, // 0 = Off, 1 = Risk Score (0-100), 2 = Risk Score & Attack History.
            'INF_ENGINE'        => 0, // Enable or disable the real-time inference engine.
            'TLS_SECURITY'      => 0, // Enable or disable transport security (TLS).
            'QUERY_TAGGING'     => 1, // Enable or disable query tagging.
            'MASK_ADDRESS'      => 1, // Anonymises the local-part of an email address (e.g. anonymous@domain.tld)
            'CUSTOM_TAG'        => '', // Specify a custom query tag instead of the default (Domain+Page).
            'BLOCKED_COUNTRIES' => array(), // Specify an array of countries or isocodes to be blocked.
            'ALLOWED_COUNTRIES' => array(), // Specify an array of countries or isocodes to be allowed.
        );

        $result_array = \proxycheck\proxycheck::check($address, $proxycheck_options);

        if (is_array($result_array) and ($result_array[$address]["proxy"] == 'yes' or $result_array["block"] == 'yes')) {
            return true;
        }

    } catch (Exception $e) {
        //echo 'Caught exception: ',  $e->getMessage(), "\n";
    }

    return false;
}
