<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20220422 09:31:53
// @desc: hosting document


namespace IGK\System\Html\Dom;

use IGK\System\Html\Css\CssUtils;

/**
 * for rendering inline css tempory file
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
    public function render($options = null)
    {        
        
        $clear = CssUtils::InitSysGlobal($this->doc);       

        $inlineTheme = $this->doc->getInlineTheme();
        $s = CssUtils::GetInlineStyleRendering($this->doc);
        // igk_wln_e("inile style ::: ", $s);
        $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
        if ($v_bindTempFiles){
            igk_css_bind_theme_files($this->doc, $inlineTheme, $v_bindTempFiles);
        }

        // igk_wln("bind temp file :", $v_bindTempFiles);

        $g = $inlineTheme->get_css_def(true);
        $vs = igk_create_node("style");
        $vs->text("\n".$g);
        $s.= $vs->render();   
        if ($clear){
            $this->doc->getSysTheme()->resetSysGlobal();
        }
        // igk_wln_e(__FILE__.":".__LINE__, "inline rendering, ", $s);
        return $s;        
    }

   
}