<?php
require_once "config.php";

class Plot {
    // 图像句柄
    var $im;

    // 用户档案
    var $profile;

    // 素材高宽
    var $sc_w;
    var $sc_h;

    // 初始化
    function init($appdata, $profile) {
        // 加载素材
        $this->im = $this->_loadSucai($appdata['sucai']);
        $this->profile = $profile;

        if (count($appdata['plot']) < 1) {
            return false;
        }

        foreach ($appdata['plot'] as $plot) {
            $this->_plot($plot);
        }
    }

    function output($file="") {
    	$imgdata = null;
        if (!$file) {
            header('Content-type: image/png');
            $imgdata = imagepng($this->im);
        } else if ($file == 'return') {
            ob_start();
            imagepng($this->im);
            $imgdata = ob_get_contents();
            ob_end_clean();
        } else {
            $imgdata = imagepng($this->im, $file);
        }
		imagedestroy($this->im);
		return $imgdata;
    }

    function _loadSucai($sucai) {
        // TODO: 检查跨目录安全
        $scfile = PATH_DATA."/sucai/$sucai";

        // TODO: 处理目录
        // 没处理

        // 创建图像句柄
        $im = imagecreatefrompng($scfile);
        return $im;
    }

    function _plot($plot) {
        list($x, $y) = explode(':', $plot['pos']);
        list($text_type, $text_pool) = explode(':', $plot['text']);
        switch($text_type) {
            case 'profile':
                $text = $this->profile[$text_pool];
                break;
            case 'rand':
                list($start, $end) = explode('-', $text_pool);
                $text = rand($start, $end);
                break;
            case 'randtext':
                $arr = explode('###', $text_pool);
                $index = rand(0, count($arr)-1);
                $text = $arr[$index];
                break;
            case 'plain':
                $text = $text_pool;
                break;
            case 'file':
                $file = preg_replace("/[^a-z\-A-F0-9_]", "", $text_pool);
                $arr = file($file, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);
                $index = rand(0, count($arr)-1);
                $text = $arr[$index];
            default:
                $text = $text_type;
        }
        $text = $plot['prepend'].$text.$plot['append'];
        $text = str_replace('\n', "\n", $text);
        $this->text = $text;

        $color = $this->_getColor($plot['color']);
        $font = PATH_DATA."/fonts/".$plot['font'];

        imagettftext($this->im, $plot['size'], $plot['angle'], $x, $y, $color, $font, $text);
    }

    function _getColor($hex) {
        $hex = trim($hex, '#');
        if (!preg_match("/^[0-9A-F]{6}$/i", $hex)) {
            $hex = '000000';
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $color = imagecolorallocate($this->im, $r, $g, $b);
        return $color;
    }
	
	function __destruct() {
		if (is_resource($this->im)) {
			imagedestroy($this->im);
		}
	}
}

