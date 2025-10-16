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

class VCaptcha
{
    public function __construct($captcha_name = '', $switch)
    {
        global $cfg;

        switch ($switch) {
            case 'easy':
                $this->c_string = 'qwertyuiop';
                $this->c_length = 5;
                $this->c_width  = 75;
                break;
            case 'normal':
                $this->c_string = 'QWERTYUIOPqwertyuiop';
                $this->c_length = 7;
                $this->c_width  = 90;
                break;
            case 'hard':
                $this->c_string = 'QWERTYUIOPqwertyuiop1234567890';
                $this->c_length = 9;
                $this->c_width  = 110;
                break;
        }
        $this->c_height = 30;
        $this->c_code   = '';

        $this->create($captcha_name, $switch);
    }
    /* render the captcha */
    public function create($captcha_name, $switch)
    {
        global $cfg;

        $s = 0;
        while ($s < $this->c_length) {
            $build = substr($this->c_string, mt_rand(0, strlen($this->c_string) - 1), 1);
            if (!strstr($this->c_code, $build)) {
                $this->c_code .= $build;
                $s++;
            }
        }
        if ($captcha_name == '') {
            $_SESSION["signin_captcha"] = $this->c_code;
        } elseif ($captcha_name != '') {
            $_SESSION[$captcha_name] = $this->c_code;
        }

        $c_image = imagecreate($this->c_width, $this->c_height);
        switch ($cfg["site_theme"]) {
            case "blue":
                $c_bg = imagecolorallocate($c_image, 38, 113, 170);
                break;
            case "green":
                $c_bg = imagecolorallocate($c_image, 26, 117, 41);
                break;
            case "orange":
                $c_bg = imagecolorallocate($c_image, 209, 88, 33);
                break;
            case "purple":
                $c_bg = imagecolorallocate($c_image, 75, 36, 114);
                break;
            case "red":
                $c_bg = imagecolorallocate($c_image, 251, 104, 96);
                break;
            default:
                $c_bg = imagecolorallocate($c_image, 0, 80, 160);
                break;
        }
        $c_txt  = imagecolorallocate($c_image, 255, 255, 255);
        $c_line = ($switch == 'easy' or $switch == 'normal') ? imagecolorallocate($c_image, 0, 0, 0) : imagecolorallocate($c_image, 0, 200, 300);

        for ($s = 0; $s < ($this->c_height * $this->c_width) / 2; $s++) {
            imagefilledellipse($c_image, mt_rand(0, $this->c_width), mt_rand(0, $this->c_width), 1, 1, $c_line);
        }

        for ($s = 0; $s < ($this->c_height * $this->c_width) / 150; $s++) {
            imageline($c_image, mt_rand(0, $this->c_height), mt_rand(0, $this->c_width), mt_rand(0, $this->c_height), mt_rand(0, $this->c_width), $c_line);
        }

        imagestring($c_image, 5, 15, 8, $this->c_code, $c_txt);

        header("Content-type: image/png");
        imagepng($c_image);
    }
}
