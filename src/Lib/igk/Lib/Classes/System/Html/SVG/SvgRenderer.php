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
        if (!self::$sm_renderList){
            if (igk_getv($options, "Document")){
                igk_reg_hook(IGKEvents::HOOK_HTML_BODY, [self::class, "RenderList"]);           
                $options->Document->setTempFlag("svg:list", []);
                self::$sm_renderList = true;
            } else if (igk_is_ajx_demand()){
                igk_reg_hook(IGKEvents::HOOK_AJX_END_RESPONSE, [self::class, "RenderList"]); 
                self::$sm_renderList = true;
            }
        }

    }
    public static function RenderList($e){
        $options = igk_getv($e->args, "options"); 
              
        if ($list =  self::$RegisterPath){
            if (igk_environment()->is("DEV"))
                echo "<!-- SVG LIST -->";
            $n = igk_createnode("div");
            $n["class"] = "igk-svg-lst";
            $n["style"] = "display:none";
            $n->host(function() use ($list){
                foreach($list as $k=>$v){
                    echo "<".$k.">";
                    echo igk_svg_content(igk_io_read_allfile($v));
                    echo "</".$k.">";
                }
            });
            
            echo $n->render($options);
            if (igk_environment()->is("DEV"))
                echo "<!-- END:SVG LIST -->";
        }
        // clear the registrated path
        self::$RegisterPath = [];
    }
    public static function RegisterIcon($name, $context=null){
        return self::svgNewIcons($name);
    }
    private static function svgNewIcons($name){
        $n = new SvgListIconNode($name);                
        return $n;
    }

}