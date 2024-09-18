<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssControllerStyleRenderer.php
// @date: 20240214 22:21:19
namespace IGK\System\Html\Css;

use Error;
use Exception;
use IGK\Controllers\BaseController;
use IGK\Css\CssCoreResponse;
use IGK\Css\CssThemeOptions;
use IGKException;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGKOb;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssControllerStyleRenderer{
    var $ctrl;
    var $defctrl;
    var $doc;
    var $debug;
    var $primaryTheme;
    /**
     * disable or not core style definition on rendering
     * @var ?bool
     */
    var $noCoreStyleDefinition;
    /**
     * theme to render with dark and light style
     * @var mixed
     */
    var $theme;

    var $doc_id;

    /**
     * render style
     * @param null|BaseController $ctrl controller base of the rendering
     * @param null|HtmlDocTheme $theme theme to render 
     * @return CssCoreResponse 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public static function RenderStyle(?BaseController $ctrl, ?HtmlDocTheme $theme=null){
        $c = new static;
        $c->ctrl = $ctrl;
        $c->doc = $ctrl->getCurrentDoc() ?? igk_app()->getDoc();
        $c->theme = $theme;
        return $c->output();
    }
    /**
     * 
     * @return CssCoreResponse 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws EnvironmentArrayException 
     * @throws Exception 
     * @throws CssParserException 
     */
    public function output(){
        ob_start();
        echo '@charset "utf-8";' . "\n";
        $debug = 1;
        $ctrl=  $this->ctrl;
        $defctrl = $this->defctrl; 
        $debug = $this->debug;
        $ref = '';
        $doc =  $this->doc;
        $cnfPath = null;
        $primaryTheme = $this->primaryTheme ?? igk_getr("theme", CssThemeOptions::DEFAULT_THEME_NAME);

        $vtheme = $this->theme ?? ($doc ? $doc->getTheme(false): null) ?? igk_die('no rendering theme');
        $is_ref_cache =  $doc_id = null;
     
        if ($debug) {
            echo ("/* ------------------------------------------------ */\n");
            echo ("/* BALAFON Css DEBUG INFO : */\n");
            echo ("/* document is sys doc ? " . ($doc ? $doc->getIsSysDoc() : 'undefined') . "*/\n");
            echo ("/* referer : " . $ref . "*/ \n");
            echo ("/* controller : " . $ctrl . "*/ \n");
            echo ("/* defctrl : -- " . $defctrl . "*/ \n");
            echo ("/* ------------------------------------------------ */\n");
        }
        if ($doc) {
            igk_set_env("sys://css/cleartemp", __FUNCTION__);
            $vsystheme = $doc->getSysTheme(); 
            $vdef = $vtheme->getDef();
            $in_config = igk_app()->settings->appInfo->config;
            $v_no_theme_rendering =  $in_config && $vtheme->getDef()->unsetStyleFlag('no_theme_rendering');
    
            if ($ref && $cnfPath) {
                if (strpos($ref, $cnfPath) !== 0) {
                    $v_no_theme_rendering = false;
                    $vtheme->getDef()->unsetStyleFlag('no_theme_rendering');
                    $vtheme->getDef()->setStyleFlag('page', null);
                }
            }
          
            // + | ----------------------------------------------------
            // + | get binding temporary theme that need to be included
            // + | clear the list before save the session
            // + | - get copy of files to include, clear list, before
            // + | - closing the session.
            $v_binTempFiles = $vdef->getBindTempFiles(1);
            $v_tempFiles = $vdef->getTempFiles(1);
            $seridata = $vtheme->to_array();
            $vtheme->reset();
            igk_sess_write_close();
    
            $vtheme->load_data($seridata);
            // + | ---------------------------------------------------------------
            // + | bind controller definition   
            if ($ctrl && !$v_no_theme_rendering) {
                // + | attach temp files first - the first time
                $ctrl->bindCssStyle($vtheme, true);
            }
            if ($v_binTempFiles) {
                igk_css_bind_theme_files($vtheme, $v_binTempFiles);
            }
            if ($v_tempFiles) {
                $dc = &$vtheme->getDef()->getTempFiles();
                array_push($dc, ...$v_tempFiles);
            }
            // + | -------------------------------------------------------------
            // + | passing data to document with css
            // + |        
            if (igk_configs()->css_view_state) {
              
                echo ("/* document " . $ref . "::::*/  body:before{content:'referer {$ref} cached: {$is_ref_cache} {$doc_id} controller : {$ctrl} ';}");
            }
    
            // + | compile render systheme
            \IGK\Css\CssThemeCompiler::CompileAndRenderTheme(
                $vsystheme,
                $doc->getId(),
                "sys:global",
                true,
                true,
                false,
                null
            );
            if ($this->noCoreStyleDefinition){
                ob_end_clean();
            } 
            // + | for checking... 
            if ($ctrl && !$v_no_theme_rendering && ($def = \IGK\System\Html\Css\CssUtils::AppendDataTheme($ctrl, $vtheme, $primaryTheme))) {
                echo implode("", $def);
            } 
        } else {
            echo "/* load - min.css */";
            include(IGK_LIB_DIR . "/" . IGK_STYLE_FOLDER . "/balafon.min.css");
        }
        $c = IGKOb::Content();
        IGKOb::Clear();
    
        if (!igk_sys_env_production() && (igk_getr("recursion") == 1)) {
            $g = igk_get_env("sys://theme/colorrecursion");
            if (igk_count($g) === 0) {
                igk_wl("/*all good no recursion detected*/");
            } else {
                igk_wl("/*\n" . implode("\n", array_keys($g)) . "\n*/");
            }
            igk_exit();
        }
        $referer = null;
        igk_header_no_cache();
        // $c.="\n /* check why color */
        $response = new \IGK\Css\CssCoreResponse($c);
        $response->cache = false;
        $response->file = $referer;
        $response->no_cache = true;
        return $response;
        
    }

}