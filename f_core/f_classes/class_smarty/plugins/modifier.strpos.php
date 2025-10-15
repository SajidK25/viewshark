<?php

/**
* Smarty plugin
*
* @package Smarty
* @subpackage PluginsModifier
*/

/**
* Smarty strpos modifier plugin
*
* Type:     modifier<br>
* Name:     strpos<br>
* Purpose:  strpos string for output
*
* @author  n/a
* @param string $string input string
* @param string $esc_type escape type
* @param string $char_set character set
* @return string escaped input string
*/
function smarty_modifier_strpos($p1, $p2) {
    return (strpos($p1, $p2) !== false);
}

?>