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
 * Name:     href_entry<br>
 * Purpose:  return an entry from the href array<br>
 * @author n/a
 * @param array
 */
function smarty_function_href_entry($array_key) { 
    require 'f_core/config.href.php';
    return $href[$array_key['key']];
}
?>