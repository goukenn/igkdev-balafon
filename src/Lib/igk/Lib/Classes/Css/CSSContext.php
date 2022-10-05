<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKCssContext.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Css;

use IGKException;
use IGKResourceUriResolver;
/**
 * context to initialize
 */
class CSSContext{
    private $ctrl;
    private $theme;
    static $sm_instance;
    private function __construct(){

    }
    public static function Init($ctrl, $theme=null){ 
        if (self::$sm_instance === null){
            self::$sm_instance = new CSSContext();

        }
        self::$sm_instance->ctrl = $ctrl;
        self::$sm_instance->theme = $theme;
        return self::$sm_instance;
    }   
    public function Resolv($file){
        $c = $this->ctrl->getDataDir().$file;
        if (file_exists($c))
            return IGKResourceUriResolver::getInstance()->resolve($c);
        return "";
    }
    public function SetClassDef($def, $classStyle, $medias=null, $type=null){
        if ($type!=null && $type!="sys"){
            throw new IGKException("Only sys is allowed for media type", 500);
        }
        if ($medias==null){
            $this->theme->xsm_screen[$def] = "({$type}.xsm_creen:".$classStyle.")";
            $this->theme->sm_screen[$def] = "({$type}.sm_creen:".$classStyle.")";
            $this->theme->lg_screen[$def] = "({$type}.xlg_creen:".$classStyle.")";
            $this->theme->xlg_screen[$def] = "({$type}.xlg_creen:".$classStyle.")";
            $this->theme->xxlg_screen[$def] = "({$type}.xxlg_creen:".$classStyle.")";
        }else {
            $this->theme->xsm_screen[$def] = "({$type}.{$medias}:".$classStyle.")";
        }
    }
}