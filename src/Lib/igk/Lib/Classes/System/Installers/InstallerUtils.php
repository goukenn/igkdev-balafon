<?php
// author: C.A.D. BONDJE DOUE
// desc: installer utility class helper
namespace IGK\System\Installers;

final class InstallerUtils
{
    private function __construct(){        
    }
    private static function GetExtraDefinition($option){
        $data = [
            "",
            "// + | ----------------------------------------------------",
            "// + | session directory ",
            'define("IGK_SESS_DIR", $appdir . "/sesstemp");',
            ""
        ];
        return implode("\n",$data);
    }
    public static function GetEntryPointSource(array $options = null ){
        if ($options===null){
            $options = [];
        }
        $src = file_get_contents(IGK_LIB_DIR."/Inc/core/index.entry_point.pinc");
        $is_primary = igk_getv($options, "is_primary") ;
        foreach([
            "{{ @author }}"=> igk_getv($options,"author", IGK_AUTHOR),
            "{{ @license }}"=> igk_getv($options,"license", "MIT License"), 
            "{{ @date }}"=> date("Y/m/d H:i:s"),
            "{{ @entry_app_dir }}"=>$is_primary ? "./" : "../",
            "{{ @app_dir }}"=>igk_getv($options, "app_dir"),
            "{{ @project_dir }}"=>igk_getv($options, "project_dir"),
            "{{ @extra_define }}"=> igk_getv($options, "is_primary") ? "" : self::GetExtraDefinition($options),

        ] as $k=>$v){
            $src = str_replace($k , $v, $src);
        }
        return $src;
    }
    public static function NoAccessDir($dir, $framework_require = 0){
        $src = "<?php\n";
        
        if ($framework_require){
            $src .= "defined('IGK_FRAMEWORK') || die('access not allowed - framework required');\n";
        } else {
            $src .= "die('access not allowed');\n";
        }
        igk_io_w2file($dir."/index.php", $src,false);
    }
}