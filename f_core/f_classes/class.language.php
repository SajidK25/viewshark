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
