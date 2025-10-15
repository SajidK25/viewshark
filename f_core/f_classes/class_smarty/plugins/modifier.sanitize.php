<?php

/**
* Smarty plugin
* 
* @package Smarty
* @subpackage PluginsModifier
*/

/**
* Smarty escape modifier plugin
* 
* Type:     modifier<br>
* Name:     sanitize<br>
* Purpose:  sanitize string for output
* 
* @author  n/a
* @param string $string input string
* @param string $esc_type escape type
* @param string $char_set character set
* @return string escaped input string
*/
function smarty_modifier_sanitize($string) {
    global $class_filter;
    return $class_filter->clr_str($string);
}

?>
