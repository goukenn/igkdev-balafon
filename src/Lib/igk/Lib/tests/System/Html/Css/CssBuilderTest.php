<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssBuilderTest.php
// @date: 20260902 09:52:59
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssClassNameDetector;
use IGK\System\Html\Css\CssParser;
use IGK\Tests\BaseTestCase;

/**
 * 
 * @package IGK\Tests\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssBuilderTest extends BaseTestCase
{
    protected function _init(?string $css = null)
    {
        $n = new CssClassNameDetector;
        if ($css) {
            if ($source =  CssParser::Parse($css)) {
                $n->map($source->to_array());
            }
        }
        return $n;
    }
    public function test_cssbuilder_render()
    {
        $detector = $this->_init();
        $this->assertEquals('', $detector->renderToCss([]));
    }
    public function test_cssbuilder_tag()
    {
        $source = CssParser::Parse('body{background-color:red;}');
        $detector = $this->_init();
        $detector->map($source->to_array());
        $this->assertEquals('body{background-color:red}' . "\n", $detector->renderToCss([]));
    }
    public function test_cssbuilder_auto_border()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['br-solid-red-10px'], $references);
        $this->assertEquals(implode("\n", [
            '.br-solid-red-10px{',
            'border:10px solid #ff0000;',
            '}'
        ]), $detector->renderToCss($references));

        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['br-red-solid-10px'], $references);
        $this->assertEquals(implode("\n", [
            '.br-red-solid-10px{',
            'border:10px solid #ff0000;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_padding()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['pad-l-10px'], $references);
        $this->assertEquals(implode("\n", [
            '.pad-l-10px{',
            'padding-left:10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_padding_default()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['pad-10'], $references);
        $this->assertEquals(implode("\n", [
            '.pad-10{',
            'padding:10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_border_radius()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['rd-10'], $references);
        $this->assertEquals(implode("\n", [
            '.rd-10{',
            'border-radius:10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_gap_2_values()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['gap-10-10'], $references);
        $this->assertEquals(implode("\n", [
            '.gap-10-10{',
            'gap:10px 10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_letter_spacing()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['ls-m10'], $references);
        $this->assertEquals(implode("\n", [
            '.ls-m10{',
            'letter-spacing:-10px;',
            '}'
        ]), $detector->renderToCss($references));
    }

    public function test_cssbuilder_pad_4_values()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['pad-10-4-10-4'], $references);
        $this->assertEquals(implode("\n", [
            '.pad-10-4-10-4{',
            'padding:10px 4px 10px 4px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_marg_negate()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['mar-m10-m10'], $references);
        $this->assertEquals(implode("\n", [
            '.mar-m10-m10{',
            'margin:-10px -10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_key_frame()
    {
        $detector = $this->_init('@keyframes bounce{ from{ left: 0px;} to { left: 100px}} .bounce{ animation: bounce 2s 50ms ease-in infinite}');
        $references = [];
        $detector->loadReferences(['bounce'], $references);
        $s = $detector->renderToCss($references);
        $this->assertEquals(implode("\n", [
            '@keyframes bounce{from{',
            'left:0px;',
            '}',
            'to{',
            'left:100px;',
            '}',
            '}',
            '.bounce{',
            'animation:bounce 2s 50ms ease-in infinite;',
            '}',
        ]), $s);
    }

    public function test_cssbuilder_lh()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['lh-m10'], $references);
        $this->assertEquals(implode("\n", [
            '.lh-m10{',
            'line-height:10px;',
            '}'
        ]), $detector->renderToCss($references));
    }
    public function test_cssbuilder_sel()
    {
        $detector = $this->_init('');
        $references = [];
        $detector->loadReferences(['sel-red'], $references);
        $s = $detector->renderToCss($references);
        $this->assertEquals(implode("\n", [
            '.sel-red::selection{',
            'color:#ff0000;',
            '}'
        ]), $s);
    }
}
