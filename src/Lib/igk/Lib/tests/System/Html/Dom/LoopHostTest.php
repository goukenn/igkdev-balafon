<?php
// @author: C.A.D. BONDJE DOUE
// @file: LoopHostTest.php
// @date: 20240123 10:31:32
namespace IGK\Tests\System\Html\Dom;

use IGK\Controllers\SysDbController;
use IGK\System\Html\HtmlNodeBuilder;
use IGK\System\Html\IHtmlNodeEvaluableExpression;
use IGK\Tests\BaseTestCase;
use IGK\System\Html\Templates\Engine\Helpers\Utility;
use IGK\System\Html\Templates\Engine\Traits\ExpressionEvalEngineTrait;

class HtmlEvalExpression implements IHtmlNodeEvaluableExpression
{
    use ExpressionEvalEngineTrait;
    private $m_value;

    public function __construct(string $content)
    {
        $this->m_value = $content;
    }
    public function getValue(): ?string
    {
        return $this->m_value;
    }

    public function evaluate($context): mixed
    {
        return self::EvalBindingExpression($this->m_value, (array)$context); // "data";
    }
}
///<summary></summary>
/**
 * 
 * @package IGK\Tests\System\Html\Dom
 * @author C.A.D. BONDJE DOUE
 */
class LoopHostTest extends BaseTestCase
{
    private function evalExpression(string $content)
    {
        return new HtmlEvalExpression($content);
    }

    /**
     * 
     * @return mixed 
     */
    private function _loop_build($data): ?string
    {
        $node = igk_create_notagnode();
        $n = new HtmlNodeBuilder($node);
        igk_debug(true);

        $n($data, null, (object)[
            'ctrl' => SysDbController::ctrl(),
            'raw' => [
                'x' => 44,
                'count' => 3
            ]
        ]);
        igk_debug(false);
        return $node->render();
    }

    public function test_loop_with_range()
    {
        $this->assertEquals(
            '<span><li>item 2</li><li>item 3</li></span><p>after 44</p>',
            $this->_loop_build([
                'span > loop(2..3)' => [
                    'li' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }
    public function test_loop_with_sub_range()
    {
        $this->assertEquals(
            '<span><ul><li>item 2</li></ul><ul><li>item 3</li></ul></span><p>after 44</p>',
            $this->_loop_build([
                'span > loop(2..3) > ul' => [
                    'li' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }
    public function test_loop_complex()
    {
        $this->assertEquals(
            '<span><ul><li>item = 2</li></ul><ul><li>item = 3</li></ul><li>select : {"x":44,"count":3}</li> item 0  item 1  item 2  </span><p>after 44</p>',
            $this->_loop_build([
                'span' => [
                    'loop(2..3) > ul' => [
                        'li' => 'item = {{ $raw }}',
                    ],
                    ['li'=>$this->evalExpression('select : {{ $raw }}')],
                    'loop([[:@raw["count"]]])' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }
    public function test_loop_complex_loop()
    {
        $this->assertEquals(
            '<span><ul><li><div>subb : 2</div><span> child .... 2</span></li></ul><ul><li><div>subb : 3</div><span> child .... 3</span></li></ul></span><p>after 44</p>',
            $this->_loop_build([
                'span' => [
                    'loop(2..3) > ul' => [
                        'li' =>[
                            'div'=>'subb : {{ $raw }}',
                            'loop($raw) > span'=>' child .... {{ $raw }}'],
                    ],
                    // ['li'=>$this->evalExpression('select : {{ $raw }}')],
                    // 'loop([[:@raw["count"]]])' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }
}
