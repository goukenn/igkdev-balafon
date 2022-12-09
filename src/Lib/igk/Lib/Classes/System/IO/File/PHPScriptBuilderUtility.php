<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PHPScriptBuilderUtility.php
// @date: 20220803 13:48:55
// @desc: 

// @file: PHPScriptBuilderUtility.php
// @author: C.A.D BONDJE DOUE
namespace IGK\System\IO\File;


abstract class PHPScriptBuilderUtility
{
    public static function MergeSource(...$sources):?string{
        if (!$sources)return null;

        $tsrc = "";
        foreach ($sources as $value) {
            if (!$value)
                continue;
            $src = file_get_contents($value);
            if (strpos($src, "<?php") === 0){
                $src = ltrim(substr($src, 5));
            }
            $tsrc.=$src;
        }
        return "<?php\n".$tsrc;
    }
/**
 * 
 * @param mixed $data 
 * @param null|string $fc 
 * @param null|string $desc 
 * @return string 
 */
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

    /**
     * remove php comment token 
     * @param string $source source
     * @return string 
     */
    public static function RemoveComment(string $source){         
        // phpinfo(); 
        $comments = \token_get_all($source);
        $src = implode("", array_map(function($m){
            if (is_array($m)){
                if (token_name($m[0]) == "T_COMMENT"){
                    return null;
                }
                return $m[1];
            }
            return $m;
        }, $comments));
  
        return $src;
    }
}