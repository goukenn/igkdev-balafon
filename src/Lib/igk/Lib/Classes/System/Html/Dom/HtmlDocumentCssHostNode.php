<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220422 09:31:53
// @desc: hosting document


namespace IGK\System\Html\Dom;

use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use Exception;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Css\CssUtils;
use IGKException;
use ReflectionException;

/**
 * for rendering inline-css tempory file
 * @package IGK\System\Html\Dom
 */
class HtmlDocumentCssHostNode extends HtmlNode{
    /**
     * 
     * @var \IGKHtmlDoc
     */
    protected $doc;

    public function __construct($doc){
        $this->doc = $doc;
    }
    public function getCanRenderTag()
    {
        return false;
    }
    public function getCanAddChilds()
    {
        return false;
    }
    /**
     * render inline css node
     * @param mixed $options 
     * @return string 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws CssParserException 
     * @throws Exception 
     * @throws EnvironmentArrayException 
     */
    public function render($options = null)
    {     
        $clear = CssUtils::InitSysGlobal($this->doc);       
        $inlineTheme = $this->doc->getInlineTheme();
        $s = "";
        
        $theme = $this->doc->getTheme();
        $g = ""; 
        $g.= $theme->get_css_def();
        $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
        if ($v_bindTempFiles){
           igk_css_bind_theme_files($inlineTheme, $v_bindTempFiles);
           $g .= $inlineTheme->get_css_def(true);
        }
        $vs = igk_create_node("style");
        $vs->text("\n".$g);
        $is_dev = igk_environment()->isDev();
        $is_dev && ($s.= "<!-- start:inline style -->");
        $s .= $vs->render();
        $is_dev && ($s.= "\n<!-- end:inline style -->");   
       
        if ($clear){
            $this->doc->getSysTheme()->resetSysGlobal();
            $theme->getDef()->clear(); 
        }  
        return $s;        
    }

   
}