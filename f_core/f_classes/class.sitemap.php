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

class VSitemap
{
    /*
     * Ensures that the appropriate files and folders are created.
     * @param string $sitemap_folder
     * @param string $sitemap_name
     * @param string $sitemap_type
     */
    public function __construct($sitemap_folder, $sitemap_name, $sitemap_type = 'generic')
    {
        global $cfg;

        $this->base_dir = $cfg['main_dir'];
        $this->base_url = $cfg['main_url'];

        $this->sitemap_folder    = $sitemap_folder;
        $this->sitemap_name      = $sitemap_name;
        $this->sitemap_uri       = $sitemap_folder . "/" . $sitemap_name;
        $this->sitemap_type      = $sitemap_type;
        $this->sitemap_increment = 1;

        // Check if the sitemap folder exists
        if (!is_dir($this->sitemap_folder)) {
            // Create a sitemap folder
            mkdir($this->sitemap_folder) or die('Unable to create sitemap folder: ' . $this->sitemap_folder);
        }
        // Check if the core exists
        if (!file_exists($sitemap_folder . "/core.xml")) {
            // Create the core
            $this->create_core($sitemap_folder);
        }
        $this->sitemap_uri = $this->sitemap_folder . '/' . $this->current_sitemap();
        // Check if the sitemap exists
        if (!file_exists($this->sitemap_uri . ".xml")) {
            // Create the sitemap
            $this->create_sitemap($this->sitemap_uri);
        }
    }

    /*
     * Creates a sitemap based on the given URI
     * @param string $sitemap_uri
     * @return boolean
     */
    private function create_sitemap($sitemap_uri)
    {
        $sitemap_uri .= ".xml";
        // Create the sitemap
        $open_sitemap_folder = fopen($sitemap_uri, "w");
        if (!$open_sitemap_folder) {
            // if there was an error
            return false;
        }
        // Close the file
        fclose($open_sitemap_folder);
        // Create the "empty" sitemap
        $create_xml = new SimpleXMLElement('<urlset></urlset>');
        $create_xml->addAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        switch ($this->sitemap_type) {
            case 'image':

                $create_xml->addAttribute("xmlns:image", "http://www.google.com/schemas/sitemap-image/1.1", '');
                $create_xml->asXML($sitemap_uri);
                file_put_contents($sitemap_uri, preg_replace('/xmlns:xmlns=""\s?/', '', file_get_contents($sitemap_uri)));
                break;
            case "video":
            case "short":
                $create_xml->addAttribute("xmlns:video", "http://www.google.com/schemas/sitemap-video/1.1", '');
                $create_xml->asXML($sitemap_uri);
                file_put_contents($sitemap_uri, preg_replace('/xmlns:xmlns=""\s?/', '', file_get_contents($sitemap_uri)));
                break;
            default:
                $create_xml->asXML($sitemap_uri);
                break;
        }

        $core_config = array(
            'type'   => 'sitemap',
            'params' => array(
                'loc' => str_replace($this->base_dir, $this->base_url, $sitemap_uri),
            ),
        );
        $this->add_core($core_config);
        // Show that it was success
        return true;
    }

    /*
     * Returns name of current sitemap
     * @param int $offset
     * @return string
     */
    private function current_sitemap($offset = 0)
    {
        // count the number of sitemaps in the folder with the same name
        $folder_data = opendir($this->sitemap_folder);
        // create an empty array
        $sitemap_folder_content = array();
        while (false !== ($entry = readdir($folder_data))) {
            $sitemap_folder_content[] = $entry;
        }
        foreach ($sitemap_folder_content as $sitemap) {
            $result = strpos($sitemap, $this->sitemap_name);
            if ($result !== false) {
                $offset++;
            }
        }
        if ($offset == 0) {
            $offset++;
        }

        return $this->sitemap_name . "_" . $offset;
    }

    /*
     * Returns name of current sitemap when incremented by one
     * @param int $offset
     * @return string
     */
    private function increment_sitemap($offset = 1)
    {
        $current = $this->current_sitemap();

        preg_match('/(.+)' . '_' . '([0-9]+)$/', $current, $match);
        return isset($match[2]) ? $match[1] . '_' . ($match[2] + 1) : $this->sitemap_name . '_' . $offset;
    }

    /*
     * Adds a node to a sitemap
     * @param array $config
     * @return boolean
     */
    public function add_node($config)
    {
        // Check the # of nodes in the sitemap
        if (strpos($this->sitemap_uri, '.xml') !== false) {
            $url = $this->sitemap_uri;
        } else {
            $url = $this->sitemap_uri . '.xml';
        }
        $xml        = simplexml_load_file($url);
        $node_count = $xml->count();

        // Count the nodes
        if ($node_count > 999) {
            $this->sitemap_uri = $this->sitemap_folder . '/' . $this->increment_sitemap();
            // Create the sitemap
            if (!$this->create_sitemap($this->sitemap_uri)) {
                return false;
            }
        }
        //Create a node
        $node = $xml->addChild($config['type']);
        // //Set the location of the URL
        foreach ($config['params'] as $param => $node_value) {
            if (is_array($node_value)) {
                $newNode = $node->addChild("Test:" . $param . ":" . $param);

                foreach ($node_value as $key => $namespace_value) {
                    if (substr($key, 0, 3) == 'tag') {$key = 'tag';}

                    $newNode->addChild("Test:" . $param . ":" . $key, $namespace_value);
                }
            } else {
                $node->addChild($param, $node_value);
            }

        }
        //Save the XML
        $xml->asXML($url);
        return true;
    }
    /*
     * Creates a core sitemap in the specified sitemap folder
     * @param string $sitemap_folder
     * @return boolean
     */
    private function create_core($sitemap_folder)
    {
        $core_uri = $sitemap_folder . "/core.xml";
        // Create the sitemap
        $open_map = fopen($core_uri, "w");
        if (!$open_map) {
            // if there was an error
            return false;
        }
        // Close the file
        fclose($open_map);
        // Create the "empty" sitemap
        $create_xml = new SimpleXMLElement('<sitemapindex></sitemapindex>');
        $create_xml->addAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9");
        $create_xml->asXML($core_uri);
        // Show that it was success
        return true;
    }

    /*
     * Adds a sitemap node in the core sitemap
     * @param array $config
     * @return boolean
     */
    private function add_core($config)
    {
        // Check the # of nodes in the sitemap
        $url = $this->sitemap_folder . '/core.xml';
        $xml = simplexml_load_file($url);
        //Create a node
        $node = $xml->addChild($config['type']);
        // //Set the location of the URL
        foreach ($config['params'] as $param => $value) {
            $node->addChild($param, $value);
        }
        //Save the XML
        $xml->asXML($url);
        return true;
    }

}
