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
/**
 * Sanitize HTML body content
 * Remove dangerous tags and attributes that can lead to security issues like
 * XSS or HTTP response splitting
 */
class VFilter
{
    // Private fields
    public $_encoding;
    public $_allowedTags;
    public $_allowJavascriptEvents;
    public $_allowJavascriptInUrls;
    public $_allowObjects;
    public $_allowScript;
    public $_allowStyle;
    public $_additionalTags;
    /**
     * Constructor
     */
    public function HTML_Sanitizer()
    {
        $this->resetAll();
    }
    /**
     * (re)set all options to default value
     */
    public function resetAll()
    {
        $this->_encoding              = 'UTF-8';
        $this->_allowDOMEvents        = false;
        $this->_allowJavascriptInUrls = false;
        $this->_allowStyle            = false;
        $this->_allowScript           = false;
        $this->_allowObjects          = false;
        $this->_allowStyle            = false;

        $this->_allowedTags = '<a><br><b><h1><h2><h3><h4><h5><h6>'
            . '<img><li><ol><p><strong><table><tr><td><th><u><ul><thead>'
            . '<tbody><tfoot><em><dd><dt><dl><span><div><del><add><i><hr>'
            . '<pre><br><blockquote><address><code><caption><abbr><acronym>'
            . '<cite><dfn><q><ins><sup><sub><kbd><samp><var><tt><small><big>'
        ;
        $this->_additionalTags = '';
    }
    /**
     * Add additional tags to allowed tags
     * @param string
     * @access public
     */
    public function addAdditionalTags($tags)
    {$this->_additionalTags .= $tags;}
    /**
     * Allow object, embed, applet and param tags in html
     * @access public
     */
    public function allowObjects()
    {$this->_allowObjects = true;}
    /**
     * Allow DOM event on DOM elements
     * @access public
     */
    public function allowDOMEvents()
    {$this->_allowDOMEvents = true;}
    /**
     * Allow script tags
     * @access public
     */
    public function allowScript()
    {$this->_allowScript = true;}
    /**
     * Allow the use of javascript: in urls
     * @access public
     */
    public function allowJavascriptInUrls()
    {$this->_allowJavascriptInUrls = true;}
    /**
     * Allow style tags and attributes
     * @access public
     */
    public function allowStyle()
    {$this->_allowStyle = true;}
    /**
     * Helper to allow all javascript related tags and attributes
     * @access public
     */
    public function allowAllJavascript()
    {
        $this->allowDOMEvents();
        $this->allowScript();
        $this->allowJavascriptInUrls();
    }
    /**
     * Allow all tags and attributes
     * @access public
     */
    public function allowAll()
    {
        $this->allowAllJavascript();
        $this->allowObjects();
        $this->allowStyle();
    }
    /**
     * Filter URLs to avoid HTTP response splitting attacks
     * @access  public
     * @param   string url
     * @return  string filtered url
     */
    public function filterHTTPResponseSplitting($url)
    {
        $dangerousCharactersPattern = '~(\r\n|\r|\n|%0a|%0d|%0D|%0A)~';
        return preg_replace($dangerousCharactersPattern, '', $url);
    }
    /**
     * Remove potential javascript in urls
     * @access  public
     * @param   string url
     * @return  string filtered url
     */
    public function removeJavascriptURL($str)
    {
        $HTML_Sanitizer_stripJavascriptURL = 'javascript:[^"]+';

        $str = preg_replace("/$HTML_Sanitizer_stripJavascriptURL/i"
            , ''
            , $str);

        return $str;
    }
    /**
     * Remove potential flaws in urls
     * @access  private
     * @param   string url
     * @return  string filtered url
     */
    public function sanitizeURL($url)
    {
        if (!$this->_allowJavascriptInUrls) {$url = $this->removeJavascriptURL($url);}
        $url = $this->filterHTTPResponseSplitting($url);

        return $url;
    }
    /**
     * Callback for PCRE
     * @access private
     * @param matches array
     * @return string
     * @see sanitizeURL
     */
    public function _sanitizeURLCallback($matches)
    {return 'href="' . $this->sanitizeURL($matches[1]) . '"';}
    /**
     * Remove potential flaws in href attributes
     * @access  private
     * @param   string html tag
     * @return  string filtered html tag
     */
    public function sanitizeHref($str)
    {
        $HTML_Sanitizer_URL = 'href="([^"]+)"';

        return preg_replace_callback("/$HTML_Sanitizer_URL/i"
            , array(&$this, '_sanitizeURLCallback')
            , $str);
    }
    /**
     * Callback for PCRE
     * @access private
     * @param matches array
     * @return string
     * @see sanitizeURL
     */
    public function _sanitizeSrcCallback($matches)
    {return 'src="' . $this->sanitizeURL($matches[1]) . '"';}
    /**
     * Remove potential flaws in href attributes
     * @access  private
     * @param   string html tag
     * @return  string filtered html tag
     */
    public function sanitizeSrc($str)
    {
        $HTML_Sanitizer_URL = 'src="([^"]+)"';

        return preg_replace_callback("/$HTML_Sanitizer_URL/i"
            , array(&$this, '_sanitizeSrcCallback')
            , $str);
    }
    /**
     * Remove dangerous attributes from html tags
     * @access  private
     * @param   string html tag
     * @return  string filtered html tag
     */
    public function removeEvilAttributes($str)
    {
        if (!$this->_allowDOMEvents) {
            $str = preg_replace_callback('/<(.*?)>/i'
                , array(&$this, '_removeDOMEventsCallback')
                , $str);
        }
        if (!$this->_allowStyle) {
            $str = preg_replace_callback('/<(.*?)>/i'
                , array(&$this, '_removeStyleCallback')
                , $str);
        }
        return $str;
    }
    /**
     * Remove DOM events attributes from html tags
     * @access  private
     * @param   string html tag
     * @return  string filtered html tag
     */
    public function removeDOMEvents($str)
    {
        $str = preg_replace('/\s*=\s*/', '=', $str);

        $HTML_Sanitizer_stripAttrib = '(onclick|ondblclick|onmousedown|'
            . 'onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|'
            . 'onkeyup|onfocus|onblur|onabort|onerror|onload)'
        ;

        $str = stripslashes(preg_replace("/$HTML_Sanitizer_stripAttrib/i"
            , 'forbidden'
            , $str));

        return $str;
    }
    /**
     * Callback for PCRE
     * @access private
     * @param matches array
     * @return string
     * @see removeDOMEvents
     */
    public function _removeDOMEventsCallback($matches)
    {return '<' . $this->removeDOMEvents($matches[1]) . '>';}
    /**
     * Remove style attributes from html tags
     * @access  private
     * @param   string html tag
     * @return  string filtered html tag
     */
    public function removeStyle($str)
    {
        $str = preg_replace('/\s*=\s*/', '=', $str);

        $HTML_Sanitizer_stripAttrib = '(style)'
        ;

        $str = stripslashes(preg_replace("/$HTML_Sanitizer_stripAttrib/i"
            , 'forbidden'
            , $str));

        return $str;
    }
    /**
     * Callback for PCRE
     * @access private
     * @param matches array
     * @return string
     * @see removeStyle
     */
    public function _removeStyleCallback($matches)
    {return '<' . $this->removeStyle($matches[1]) . '>';}
    /**
     * Remove dangerous HTML tags
     * @access  private
     * @param   string html code
     * @return  string filtered url
     */
    public function removeEvilTags($str)
    {
        $allowedTags = $this->_allowedTags;

        if ($this->_allowScript) {$allowedTags .= '<script>';}
        if ($this->_allowStyle) {$allowedTags .= '<style>';}
        if ($this->_allowObjects) {$allowedTags .= '<object><embed><applet><param>';}

        $allowedTags .= $this->_additionalTags;
        $str = strip_tags($str, $allowedTags);

        return $str;
    }
    public function removeSQLTags($str)
    {
        $str = str_ireplace(array('CONCAT', 'ELT(', 'INFORMATION_SCHEMA'), array('', '', ''), $str);

        return $str;
    }
    /**
     * Sanitize HTML
     *  remove dangerous tags and attributes
     *  clean urls
     * @access  public
     * @param   string html code
     * @return  string sanitized html code
     */
    public function sanitize($html)
    {
        $html = $this->removeEvilTags($html);
        $html = $this->removeEvilAttributes($html);
        $html = $this->sanitizeHref($html);
        $html = $this->sanitizeSrc($html);
        $html = $this->removeSQLTags($html);

        return $html;
    }

    public function clr_str($str)
    {
        static $san = null;
        if (empty($san)) {$san = new VFilter;}
        $str = trim($str);

        return htmlspecialchars($san->sanitize($str), ENT_QUOTES, $this->_encoding);
    }
}

function html_sanitize($str)
{
    static $san = null;
    if (empty($san)) {$san = new VFilter;}
    return $san->sanitize($str);
}
