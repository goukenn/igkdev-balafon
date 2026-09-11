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
        if (!$doc instanceof IGKHtmlDoc) {
            return;
        }
        $s = "";
        $g = "";
        $clear = ($doc instanceof IGKHtmlDoc) ? CssUtils::InitSysGlobal($doc) : null;
        $theme = $doc->getTheme();
        $inlineTheme = $doc->getInlineTheme();
        igk_css_load_theme($theme);
        if ($doc->noCoreCss) {
            $theme_tab = CssParser::Parse($theme->render())->to_array();
            if (!empty($theme_tab)) { 
                $imported = array_merge([
                        'dispn',
                        'fit',
                        'igk-device',
                        'igk-media-type', 
                    ],
                    $theme->getImportedDefinitions() ?? [],
                    CssClassNameDetector::RetrieveInjectedClassesList($theme_tab)?? [], 
                    );                 
                $tab = CssParser::Parse($doc->getSysTheme()->render())->to_array();
                $g .= CssClassNameDetector::RenderMaps([
                    $tab,
                    $theme_tab,
                ],$imported);                 
            }
        } else
            $g .= $theme->get_css_def();
        $v_bindTempFiles = $inlineTheme->getDef()->getBindTempFiles(0);
        if ($v_bindTempFiles) {
            igk_css_bind_theme_files($inlineTheme, $v_bindTempFiles);
            $g .= $inlineTheme->get_css_def(true);
        }
        $is_dev = igk_environment()->isDev();
        if ($is_dev) {
            $g .= "\n" . CssClassBuffer::getInstance()->renderExtraDefinition(null, (object)[
                "lf" => ""
            ]);
        }
        if (!empty(trim($g))) {
            $vs = igk_create_node("style");
            $vs->text("\n" . $g);
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
}
