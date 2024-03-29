<?php
// @author: C.A.D. BONDJE DOUE
// desc: installer utility class helper
namespace IGK\System\Installers;

use IGK\System\IO\StringBuilder;
use IGKException;

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
    /**
     * get entry point default sources
     * @param array|null $options used options
     * @return string|false 
     * @throws IGKException 
     */
    public static function GetEntryPointSource(array $options = null ){
        if ($options===null){
            $options = [];
        }
        $src = file_get_contents(IGK_LIB_DIR."/Inc/core/index.entry_point.princ");
        $is_primary = igk_getv($options, "is_primary") ;
        foreach([
            "{{ @author }}"=> igk_getv($options,"author", IGK_AUTHOR),
            "{{ @license }}"=> igk_getv($options,"license", "MIT License"), 
            "{{ @date }}"=> date("Y/m/d H:i:s"),
            "{{ @entry_app_dir }}"=>$is_primary ? "./" : "../",
            "{{ @app_dir }}"=>igk_getv($options, "app_dir"),
            "{{ @app_config }}"=>self::GetConfigData($options),
            "{{ @project_dir }}"=>igk_getv($options, "project_dir"),
            "{{ @extra_define }}"=> igk_getv($options, "is_primary") ? "" : self::GetExtraDefinition($options),

        ] as $k=>$v){
            $src = str_replace($k , $v, $src);
        }
        return $src;
    }
    public static function GetConfigData(array $options):?string{
        $sb = new StringBuilder;
        if (igk_getv($options, "no_subdomain")){
            $sb->appendLine('define(\'IGK_NO_SUBDOMAIN\', 1);');
        }
        if (igk_getv($options, "no_webconfig")){
            $sb->appendLine('define(\'IGK_NO_WEBCONFIG\', 1);');
        }
        return trim($sb.'');
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