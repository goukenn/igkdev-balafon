<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetectorTest.php
// @date: 20240913 09:35:04
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\Compiler\CssThemeBuilder;
use IGK\System\Html\Css\CssClassNameDetector;
use IGK\System\Html\Css\CssClassNameDetectorUtils;
use IGK\System\Html\Css\CssParser;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\Tests\BaseTestCase;

/**
 * auto generate doc.
 * @package IGK\Tests\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssClassNameDetectorTest extends BaseTestCase
{
    protected function _compileDefinition(string $txt)
    {
        $tab = CssParser::Parse($txt);
        $detector = new CssClassNameDetector;
        $detector->map($tab->to_array());
        $references = [];
        $detector->loadReferences(['flex-container'], $references);
        $css = $detector->renderToCss($references, ['lf' => '']);
        return $css;
    }
    public function test_cssclassdetector_support(){
        $sb = $this->_compileDefinition('@supports(display:   flex){ .flex-container{ color:red; }}');
        $this->assertEquals('@supports(display: flex){.flex-container{color:red}}', $sb);
    }
    /**
     * Tests cssclassdetector detect class.
     */
    public function test_cssclassdetector_detect_class()
    {
        $parser = CssParser::Parse(".card{ display:block; }");
        $detector = new CssClassNameDetector;
        $detector->map($parser->to_array());
        $references = [];
        $arr = $detector->resolv("info card", $references);
        $this->assertTrue(is_array($arr));
        $this->assertEquals(
            ".card{display:block}",
            $detector->renderToCss($arr, (object)["lf" => ""])
        );
    }
    /**
     * Tests cssclassdetector detect class php.
     */
    public function test_cssclassdetector_detect_class_php()
    {
        $parser = CssParser::Parse(".card{ display:block; } div.container{ width:auto;} div.info{height:3em;} @media (max-width:420px){ div.info{  } .card{ display: flex;}}");
        $detector = new CssClassNameDetector;
        $detector->map($parser->to_array());
        $arr = CssClassNameDetectorUtils::DetectFromPhpSource(
            $detector,
            <<<'PHP'
<?php
$a = "presentation info";
$b = "<html className=\"card\"></html>";
?><div class="info">for information</div>
PHP
        );



        $this->assertTrue(is_array($arr));
        $this->assertEquals(
            ".card{display:block}div.info{height:3em}@media(max-width:420px){.card{display:flex}}",
            $detector->renderToCss($arr, (object)["lf" => ""])
        );
    }
    /**
     * Tests css node multiclass.
     */
    public function test_css_node_multiclass()
    {
        $n = igk_create_node('div');
        $n['class'] = 'fitw.fit no-overflow posab';
        $s = $n->render();
        $this->assertTrue($s == '<div class="fitw fit no-overflow posab"></div>', 'invalid');
    }

    public function test_cssnamedetector_from_class_to()
    {
        $detector = new CssClassNameDetector;
        $r = $detector->getReference('fs-\[164px\]');
        $this->assertEquals('{".fs-\\\\[164px\\\\]":{"font-size":"164px"}}', json_encode($r, JSON_UNESCAPED_SLASHES));
    }


    /**
     * 
     * @param mixed $theme 
     * @return string 
     */
    protected function _renderTheme($theme)
    {
        $theme_tab = CssParser::Parse($theme->render(true))->to_array();
        $imported = CssThemeBuilder::GetImported($theme, $theme_tab);
        $ts = CssClassNameDetector::RenderMaps([$theme_tab], $imported, ['lf' => '']);
        return $ts;
    }
    public function test_cssclassnamedetector_renderbody()
    {
        $theme = new HtmlDocTheme;
        $theme['body'] = 'color:red';
        $this->assertEquals('body{color:red}', $this->_renderTheme($theme));
    }
    public function test_cssclassnamedetector_rendertheme()
    {
        $theme = new HtmlDocTheme;
        $theme['Html[data-theme="dark"]    body'] = 'color:red';
        $this->assertEquals('Html[data-theme="dark"] body{color:red}', $this->_renderTheme($theme));
    }
    public function test_cssclassnamedetector_merge_definition()
    {
        $theme = new HtmlDocTheme;
        $theme['body.igk-body'] = 'background-color:red;';
        $sys_theme = new HtmlDocTheme;
        $sys_theme['body.igk-body'] = 'background-color:indigo;';
        $maps = [
            CssParser::Parse($sys_theme->render(true))->to_array(), // revious map 
            CssParser::Parse($theme->render(true))->to_array(), // new map
        ];
        $imported = ['igk-body'];
        $ts = CssClassNameDetector::RenderMaps($maps, $imported, ['lf' => '']);
        $this->assertEquals('body.igk-body{background-color:red}', $ts);
    }
}
