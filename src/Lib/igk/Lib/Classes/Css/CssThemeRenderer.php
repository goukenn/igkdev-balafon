<?php

// @author: C.A.D. BONDJE DOUE
// @filename: CssThemeRenderer.php
// @date: 20220828 15:17:14
// @desc: 


namespace IGK\Css;

use IGK\System\Diagnostics\Benchmark;

/**
 * use to render theme
 * @package 
 */
class CssThemeRenderer
{
    /**
     * 
     * @var bool
     */
    var $minfile = false;
    /**
     * document 
     * @var \IGK\System\Html\Dom\HtmlDocumentNode
     */
    var $doc;
    /**
     * enable or not resource resolution
     * @var mixed
     */
    var $themeExport;

    /**
     * resource resolver
     * @var ICssResourceResolver
     */
    var $resourceResolver;
    /**
     * export theme
     * @var bool
     */
    var $exportTheme = false;
    /**
     * controller to bind data 
     * @var mixed
     */
    var $controller;
    /**
     * must set global id
     * @var string
     */
    private $m_globalId;
    /**
     * no
     * @var no systheme
     */
    private $m_noSysTheme;


    /**
     * 
     * @param string $globaId global id
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function __construct(string $globaId)
    {
        if (empty($globaId)) {
            igk_die("must set global id");
        }
        $this->m_globalId = $globaId;
    }

    private function _renderSystemTheme()
    {
        $s = "";
        ob_start();
        $vsystheme = $this->doc->getSysTheme();
        $no_systheme = \IGK\Css\CssThemeCompiler::CompileAndRenderTheme(
            $vsystheme,
            $this->m_globalId,
            "sys:global",
            false,
            $this->minfile,
            $this->exportTheme,
            $this->resourceResolver
        );
        $this->m_noSysTheme =  $no_systheme;
        $s .= ob_get_contents();
        ob_end_clean();
        return $s;
    }

    /**
     * render global theme [sysTheme] + [controller theme] ?
     */
    public function render()
    {
        $s = $this->_renderSystemTheme();
        list($minfile, $doc, $no_systheme, $themeexport)
            = [$this->minfile, $this->doc, $this->m_noSysTheme, $this->exportTheme];

        $o = IGK_STR_EMPTY;
        $srh = array();
        $el = $minfile ? IGK_STR_EMPTY : IGK_LF;
        $data = array();

        $theme = $doc->getTheme();
        if ($this->controller) {
            $this->controller->bindCssStyle($theme, true);
        }

        if (!$no_systheme) {
            $data[] = array("name" => "systheme", "theme" => $doc->getSysTheme());
            $data[] = igk_css_init_style_def_workflow($doc);
        } else {
            $data[] = array("name" => "maintheme", "theme" => $doc->getTheme());
        }
        foreach ($data as $v) {
            $theme = $v["theme"];
            $name = $v["name"];
            $o = "";
            Benchmark::mark("theme-export");
            $o .= "/* CSS - [\"{$name}\"] */" . $el;
            $o .= $theme->get_css_def($minfile, $themeexport) . $el;
            Benchmark::expect("theme-export", 0.500);
            $srh[] = $o;
            if (isset($v["doc"])) {
                $srh[] = $v["doc"]->getTemporaryCssDef($minfile, $themeexport) . $el;
            }
        }

        $o = $s . PHP_EOL . implode(PHP_EOL, $srh);
        return $o;
    }
}
