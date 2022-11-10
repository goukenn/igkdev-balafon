<?php

namespace IGK\System\Html\Css;

use IGK\System\IO\Configuration\ConfigurationReader;

/**
 * gobal css parser
 * @package IGK\System\Html\Css
 */
class GlobalCssParser{
    var $definition;
    var $source;
    private function __construct()
    {
        
    }
    public static function Parse(string $content){
        $ln = strlen($content);
        $pos = 0;
        $pg = [];
        $selector = "";
        $reader = ConfigurationReader::CreateCssValueReader();
        while($ln> $pos){
            $ch = $content[$pos];
            switch($ch){
                case "{":
                    $g = igk_str_read_brank($content, $pos,"}", "{",null, 0, 0);                    
                    if (empty($selector)){
                        $selector = "::globlal";
                    }
                    $g = trim(substr($g, 1, strlen($g)-2));
                    $selector = trim($selector);
                    $pg[$selector] = $reader->read($g);
                    $selector = null;
                    break;
                default:
                    $selector .= $ch;
                break;
            }
            $pos++;
        }
        if (count($pg) > 0){
            $def = new self;
            $def->definition = $pg;
            $def->source = $content;

        }
        return $def;
    }
}