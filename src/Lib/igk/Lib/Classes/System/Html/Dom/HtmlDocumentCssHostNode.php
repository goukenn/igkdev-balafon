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
use IGK\System\Html\Css\Compiler\CssThemeBuilder;
use IGK\System\Html\Css\CssClassBuffer;
use IGK\System\Html\Css\CssClassNameDetector; 
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Css\CssUtils;
use IGKException;
use IGKHtmlDoc;
use ReflectionException;

/**
 * for rendering inline-css tempory file
 * @package IGK\System\Html\Dom
 */
class HtmlDocumentCssHostNode extends HtmlNode
{
    /**
     * auto generate doc.
     * @var \IGKHtmlDoc
     */
    protected $doc;
    /**
     * .ctr
     * @param IGKHtmlDoc $doc
     */
    public function __construct(IGKHtmlDoc $doc)
    {
        $this->doc = $doc;
    }
    /**
     * Returns Can Render Tag.
     */
    public function getCanRenderTag()
    {
        return false;
    }
    /**
     * Returns Can Add Childs.
     */
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
        $doc = $this->doc;
        $v_ops = igk_environment()->isOPS();
        $v_nocorecss = $doc->noCoreCss;
        if ((!$doc instanceof IGKHtmlDoc) ||($v_ops && $v_nocorecss)) {
            return;
        }
        $v_use_core = $v_nocorecss && !$doc->noColorPalette; 
        if ($v_ops && $v_nocorecss){
            return;
        }
        

        $s = "";
        $g = "";
        $clear = CssUtils::InitSysGlobal($doc);
        $theme = $doc->getTheme();
        $inlineTheme = $doc->getInlineTheme();
        igk_css_load_theme($theme);         
        $imported =null;
        $sb = CssUtils::RenderAfterCheckColorTheme($theme);
        $v_maps = [
            $theme_tab = CssParser::Parse($sb)->to_array()
        ];
        $tab = CssParser::Parse($doc->getSysTheme()->render())->to_array();
        $opt = ['lf'=> '', 'useDetectedClass'=>true];
        $imported = self::_GetImported($theme, $theme_tab);
        if ($v_use_core) {
            if (!empty($theme_tab)) {
                array_unshift($v_maps, $tab);
            }
        }
        // $sb = CssUtils::RemoveUnsetProperties($sb);            
        $nsb = CssClassNameDetector::RenderMaps($v_maps, $imported, $opt);            
        $g .= $nsb;
        
        $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
        if ($v_bindTempFiles) {
            igk_css_bind_theme_files($inlineTheme, $v_bindTempFiles);
            $g .= $inlineTheme->get_css_def(true);
        }
        $is_dev = igk_environment()->isDev(); 
        $g .= $extra =   self::RenderExtraDefinition($imported);
        // igk_dev_wln_e(__FILE__.":".__LINE__ , $imported, $extra);
        $g = trim($g); 
        if (!empty($g)) {
            $g = implode("\n", ['@charset "UTF-8";', trim($g)]);
            $vs = igk_create_node("style");
            $vs->text(implode("\n",[$g, '/* end style */']));
            $is_dev && ($s .= "<!-- start:inline style -->");
            $s .= $vs->render();
            $is_dev && ($s .= "\n<!-- end:inline style -->");
        }
        if ($clear) {
            $this->doc->getSysTheme()->resetSysGlobal();
            $theme->getDef()->clear();
        } 
        return $s;
    }
    /**
     * retrieve imported data
     * @param mixed $theme 
     * @param mixed $theme_tab 
     * @return array 
     */
    private static function _GetImported($theme, $theme_tab)
    {
        return CssThemeBuilder::GetImported($theme, $theme_tab, true);
    }
    /**
     * reder extra definition 
     * @param mixed $imported
     * @return ?string 
     */
    public static function RenderExtraDefinition($imported)
    {
        $i = CssClassBuffer::getInstance();
        $g = "\n" . $i->renderExtraDefinition(null, (object)[
            "lf" => "",
            "noCharsetHeader" => true,
            'imported'=>$imported
        ]) . "\n";
        if (!empty(trim($g))) {
            return $g;
        }
    }
}
