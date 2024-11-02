<?php
// @author: C.A.D. BONDJE DOUE
// @file: ThemeRenderingTest.php
// @date: 20240215 04:54:53
namespace IGK\Tests\System\Html\Css;

use IGK\Controllers\BaseController;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Exceptions\CssParserException;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Css\CssControllerStyleRenderer;
use IGK\System\Html\Css\CssUtils;
use IGK\System\Html\Dom\HtmlDocTheme;
use IGK\Tests\BaseTestCase;
use IGKException;
use ReflectionException;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class ThemeRenderingTest extends BaseTestCase{
    private $m_root;

    public function setUp():void{ 
    }
    /**
     * test creation 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws CssParserException 
     * @throws EnvironmentArrayException 
     * @throws InvalidArgumentException 
     * @throws ExpectationFailedException 
     */
    public function test_empty_render(){
        $theme = new HtmlDocTheme( null, "test");
        $s = $theme->get_css_def();
        $this->assertEquals('', $s);
    }
    public function test_empty_render_no_semicolumn(){
        $theme = new HtmlDocTheme( null, "test");
        $def = $theme->getDef();
        $def[".igk-fsl-4"] = "font-size:2.8em";
        $def[".igk-fsl-5"] = "font-size:4.8em";
     
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('.igk-fsl-4{font-size:2.8em;}.igk-fsl-5{font-size:4.8em;}', $s);
    }
    public function test_empty_render_replace(){
        $theme = new HtmlDocTheme( null, "test");
        $def = $theme->getDef();
        $def[".igk-fsl-4"] = "font-size:2.8em";
        $def[".igk-fsl-4"] = "font-size:4.8em";
     
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('.igk-fsl-4{font-size:4.8em;}', $s);
    }

    public function test_cssrendering_treatbranket(){
        $theme = new HtmlDocTheme(null, 'test');
        // $theme['.basic'] = '{sys:dispib, alignc}; [bgcl:--fillcolor]'; 
        $theme->def[".igk-progressbar"] = "{sys:dispib, alignc}; [bgcl: progressBarBackgroundColor, #444]; height:16px;";
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('.igk-progressbar{background-color:progressBarBackgroundColor;height:16px;}', $s);
    }
    public function test_cssrendering_maptheme(){
        $theme = new HtmlDocTheme(null, 'test');
        $theme['.igk-progressbar'] = '{sys:dispib, alignc}; [bgcl: progressBarBackgroundColor, #444] height:16px;';
        $medias = null;
        $tab = $theme->getdef()->getAttributes() ?? [];
        CssUtils::MapMediaCssTheme($theme, 'dark',  $tab, $medias); 
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('html[data-theme=\'dark\'] .igk-progressbar{background-color:progressBarBackgroundColor;}', $s);
    }
    public function test_cssrendering_maptheme_include(){
        $theme = new HtmlDocTheme(null, 'test');
        
        $theme['.igk-progressbar'] = '(sys:.igk-def-c); overflow:hidden;';
        $medias = null;
        $tab = $theme->getdef()->getAttributes() ?? [];
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('.igk-progressbar{overflow:hidden;}', $s);
        CssUtils::MapMediaCssTheme($theme, 'dark',  $tab, $medias); 
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('', $s);
    }
    public function test_cssrendering_maptheme_bar(){ 
        $theme = new HtmlDocTheme(null, 'test');
        $tab = [];
        $tab['.basic'] = "content:' *'; display:inline-block; white-space:pre; [fcl:igk-required-mark-fcl]"; 
        CssUtils::MapMediaCssTheme(
            $theme,
            'dark',
            $tab,
            [],
            false
        ); 
        $s = $this->_out_theme($theme); 
        $this->assertEquals('html[data-theme=\'dark\'] .basic{color:igk-required-mark-fcl;}', $s);
    }

    public function test_cssrendering_maptheme_3(){
        $theme = new HtmlDocTheme(null, 'test');
        $theme['.igk-progressbar'] = '{sys:dispib, alignc}; [bgcl: progressBarBackgroundColor, #444] {sys:fitw} height:16px; color: [cl:red]';
        $medias = null;
        $tab = $theme->getdef()->getAttributes();
        CssUtils::MapMediaCssTheme($theme, 'dark',  $tab, $medias); 
        $s = $this->_out_theme($theme); 
        $this->assertEquals('html[data-theme=\'dark\'] .igk-progressbar{background-color:progressBarBackgroundColor;color:red;}', $s);
    }
    private function _out_theme($theme){
        $this->m_root = new HtmlDocTheme(null, 'root-css-test-theme');
        return $theme->get_css_def(true, true, null, null, $this->m_root);
    }
    /**
     * test rendering body 
     */
    public function test_theme_render_body(){
        // + | --------------------------------------------------------------------
        // + | theme createion
        // + |
        
        $theme = new HtmlDocTheme( null, "test");
        $theme['body'] = 'background-color:red;';
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('body{background-color:red;}', $s);

        // + | --------------------------------------------------------------------
        // + | append extra property - order property and merge
        // + |
        
        $theme['body:after'] = 'content:\'after\';background-color:red;'; 
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('body{background-color:red;}body:after{background-color:red;content:\'after\';}', 
        $s, 'missing ordered property');

        // + | --------------------------------------------------------------------
        // + | check replacement append style property - treat on render 
        // + |
        $theme['body:after'] = 'display:block; content:\'rp\';';
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('body{background-color:red;}body:after{background-color:red;content:\'rp\';display:block;}', 
        $s, 'missing replace and append style property property');
    }

    public function test_theme_render(){
        $theme = new HtmlDocTheme( null, "test");
        $theme->setColors(
            [
                '--igk-red'=>"#cf3232"
            ]
        );
        $theme['body'] = 'background-color:[cl:--igk-red];';
        $s = $theme->get_css_def(true, true);
        $this->assertEquals('body{background-color:#cf3232;}:root{--igk-red:#cf3232}', $s);
    }

    public function test_controller_theme_render(){
        require_once IGK_LIB_DIR.'/Styles/igk_css_colors.phtml';
        $theme = new HtmlDocTheme( null, "test");
        $theme->setColors(
                [
                '--igk-red'=>"#cf3232"
                ]
            );
        $theme['body'] = 'background-color:[cl:--igk-red];';
        $theme['body:after'] = 'content:\'content\'; display:block';//background-color:[cl:--igk-red];';
        $support = $theme->supports('backdrop-filter: blur(2px)');
        $support['backgrop-filter'] = 'blur(4px)';
        $theme->setThemeColors([
            'dark'=>[
                '--igk-red'=>'red'
            ],
            'light'=>[
                '--igk-red'=>'yellow'
            ]
        ]);
        $g = CssControllerStyleRenderer::RenderStyle(MockThemeRenderer::ctrl(), $theme); 
        ob_start();
        $g->render().''; 
        $s = ob_get_contents();
        ob_end_clean(); 
        $this->assertTrue(
            false !== strpos($s, "html[data-theme='dark']")
        );
    }
}


class MockThemeRenderer extends BaseController{

}