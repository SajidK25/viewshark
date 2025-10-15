<?php

/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

/**
* Smarty md5 modifier plugin
*
* Type:     modifier<br>
* Name:     md5<br>
* Purpose:  md5 string for output
*
* @author  n/a
* @param string $string input string
* @param string $esc_type escape type
* @param string $char_set character set
* @return string escaped input string
*/
function smarty_modifier_md5($string) {
    return md5($string);
}

?>