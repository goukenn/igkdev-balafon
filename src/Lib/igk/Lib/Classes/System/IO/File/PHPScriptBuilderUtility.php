<?php

namespace IGK\System\IO\File;


class PHPScriptBuilderUtility
{

    public static function GetArrayReturn($data, $fc=null){
        $o  = "<?php\n";
        $o .= "// @desc: list controller configuration cache\n";
        if ($fc)
        $o .= "// @file: ".basename($fc)."\n";
        $o .= "// @file: ".date("Y-m-d")."\n";
        $o .= "// @author: ". IGK_AUTHOR."\n";
        $o .= "return [".$data."];";
        return $o;
    }
}