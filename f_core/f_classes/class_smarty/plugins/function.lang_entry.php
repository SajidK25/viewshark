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
 * Name:     language_entry<br>
 * Purpose:  return an entry from the language file(s)<br>
 * @author n/a
 * @param array
 */
function smarty_function_lang_entry($array_key) { 
    global $language;
    return $language[$array_key['key']];
}
?>