<?php

namespace IGK\System\IO\File;


class PHPScriptBuilderUtility
{

    public static function GetArrayReturn($data, ?string $fc=null , ?string $desc=null){
        $o  = "<?php\n";
        if ($desc)
            $o .= "// @desc: ".$desc."\n";
        if ($fc)
             $o .= "// @file: ".basename($fc)."\n";
        $o .= "// @file: ".date("Y-m-d")."\n";
        $o .= "// @author: ". IGK_AUTHOR."\n";
        $o .= "return [".$data."];";
        return $o;
    }
}