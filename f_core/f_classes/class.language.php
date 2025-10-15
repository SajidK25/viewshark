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

defined('_ISVALID') or header('Location: /error');

class VLanguage
{
    public $language_file;
    /* include language file */
    public function setLanguageFile($section, $short_filename)
    {
        global $cfg;

        $_l = $section == 'frontend' ? 'fe_lang' : 'be_lang';

        return $this->language_file = 'f_data/data_languages/' . $_SESSION[$_l] . '/lang_' . $section . '/' . $short_filename . '.php';
    }
    /* get a language entry */
    public function getLanguageEntry($array_key)
    {
        global $language;

        return $language[$array_key];
    }
}
