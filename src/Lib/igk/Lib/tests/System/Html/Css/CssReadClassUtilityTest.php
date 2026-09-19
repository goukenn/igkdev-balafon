<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssReadClassUtilityTest.php
// @date: 20260918 09:58:44
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssClassNameUtility;
use IGK\Tests\BaseTestCase;

/**
 * 
 * @package IGK\Tests\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
class CssReadClassUtilityTest extends BaseTestCase
{
    public function test_readclassname_readname()
    {
        $this->assertEquals(
            'info',
            CssClassNameUtility::ReadClassName('info')
        );
    }
    public function test_readclassname_with_escaped()
    {
        $this->assertEquals(
            'info\[b\]',
            CssClassNameUtility::ReadClassName('info\[b\]')
        );
    }
    public function test_readclassname_with_escaped_end()
    {
        $this->assertEquals(
            'info\[b\]',
            CssClassNameUtility::ReadClassName('info\[b\]:hover')
        );
    }
    public function test_readclassname_from_definition()
    {

        foreach (
            [
                'info' => null,
                'div.container' => '.container',
                'div.sample\:hover.ok' => '.sample\:hover',
                'div.sample\.hover.ok' => '.sample\.hover',
                'div.fs-14pt' => '.fs-14pt',
                'div.fs-14pt[ calc(34em) ]' => '.fs-14pt', // attrib not member of class definition 
                'div.fs-14pt\[ calc(34em) \]' => '.fs-14pt\[ calc(34em) \]', // attrib not member of class definition 
            ] as $l => $v
        ) {
            $this->assertFalse(($r = igk_getv(CssClassNameUtility::ReadAllDetectedClassesFromSourceDefinition($l), 0)) != $v,
                json_encode([
                    'value' => $l,
                    'expected' => $v,
                    'result' => $r
                ])
            );
        }
    }
}
