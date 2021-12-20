<?php


namespace IGK\System\Html\SVG;

use IGK\Helper\IO;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGKEvents;

/**
 * document page svg list renderer
 */
class SvgRenderer{

    public static $RegisterPath = [];
    private static $sm_renderList = false;
    const FOLDER = __CLASS__."::svgLibFolder";


     /**
     * return svg folder 
     * @return mixed 
     */
    public static function GetPath($name){
        if (!empty($name)){
            $f = self::GetSvgFolder(); 
            while( $d = array_shift($f)){
                if (file_exists( $file = $d."/".$name.".svg")){
                    return IO::GetDir($file);
                }
            }
        }
        return false;
    }

    /**
     * return svg folder 
     * @return mixed 
     */
    public static function GetSvgFolder(){
        $svg_folder = igk_environment()->get(self::FOLDER) ?? [];
        $svg_folder[] = IGK_LIB_DIR . "/Data/R/svg/icons";
        return $svg_folder;
    }
    /**
     * register folder 
     * @param mixed $folder 
     * @return void 
     * @throws EnvironmentArrayException 
     */
    public static function RegisterFolder($folder){
        if (is_dir($folder)){
            igk_environment()->push(self::FOLDER, $folder);
        }
    }
    /**
     * check if the svg icons exists is registrated folder
     * @param mixed $name 
     * @return bool 
     */
    public static function Exists($name){
        $f = self::GetSvgFolder();
        while( $d = array_shift($f)){
            if (file_exists($d."/".$name)){
                return true;
            }
        }
        return false;
    }
    public static function AcceptRenderList($options){
         
        if (igk_getv($options, "Document") && !self::$sm_renderList){
            igk_reg_hook(IGKEvents::HOOK_HTML_BODY, [self::class, "RenderList"]);
            $options->Document->setTempFlag("svg:list", []);
            self::$sm_renderList = true;
        }        
    }
    public static function RenderList($e){
        $options = $e->args["options"];        
        if ($list =  self::$RegisterPath){
            echo "<!-- SVG LIST -->";
            $n = igk_createnode("div");
            $n["class"] = "igk-svg-lst";
            // $n["style"] = "display:none";
            $n->host(function() use ($list){
                foreach($list as $k=>$v){
                    echo "<".$k.">";
                    echo igk_svg_content(igk_io_read_allfile($v));
                    echo "</".$k.">";
                }
            });
            echo $n->render($options);
        }
        // clear the registrated path
        self::$RegisterPath = [];
    }
    public static function RegisterIcon($name, $context=null){
        return self::svg_new_icons($name);
    }
    private static function svg_new_icons($name){
        $n = new SvgListIconNode($name);                
        return $n;
    }

    /*
$c = igk_environment()->{IGK_SVG_REGNODE_KEY};
    if ($p = igk_environment()->get("svg_icons_resolver")) {
        if ($m = $p->resolve($name)) {
            $n = igk_createnode("div");
            $n["class"] = "igk-svg-lst-i";
            $n["igk:svg-name"] = $name;
            $n->setCallback("AcceptRender", $m);
            return $n;
        }
    }
    if ($c === null) {
        igk_svg_register_icons(igk_app()->getDoc(), $name);
        $c = igk_environment()->{IGK_SVG_REGNODE_KEY}
            ?? igk_die("failed to used svg registrating node");
        (function () {
            $n = igk_createnode("div");
            igk_set_env("sys://node/svg_regnode", $n);
            return $n;
        })();
    } else {
        igk_svg_register_icons(igk_app()->getDoc(), $name);
    }
    $n = igk_createnode("div");
    $n["class"] = "igk-svg-lst-i";
    $n["igk:svg-name"] = $name;
    igk_svg_bind_name($name, $context);
    $fc = "igk_svg_use_callback";
    if (!igk_get_env(__FUNCTION__)) {
        $n->setCallback("AcceptRender", $fc);
        igk_set_env(__FUNCTION__, 1);
    }
    igk_set_env($fc, null);
    return $n;
    */
}