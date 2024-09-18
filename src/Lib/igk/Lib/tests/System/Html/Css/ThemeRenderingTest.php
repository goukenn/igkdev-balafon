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
    /**
     * test rendering body 
     */
    public function test_body_render(){
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
        $this->assertEquals('body{background-color:#cf3232;}', $s);
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