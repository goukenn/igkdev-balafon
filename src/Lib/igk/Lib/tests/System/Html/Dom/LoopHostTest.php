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

/**
* Html eval expression.
* @package IGK\Tests\System\Html\Dom
*/
class HtmlEvalExpression implements IHtmlNodeEvaluableExpression
{
    use ExpressionEvalEngineTrait;

    /**
    * Property: value.
    * @var mixed
    */
    private $m_value;

    /**
    * .ctr
    * @param string $content
    */
    public function __construct(string $content)
    {
        $this->m_value = $content;
    }

    /**
    * Returns Value.
    * @return ?string
    */

    public function getValue(): ?string
    {
        return $this->m_value;
    }

    /**
    * Evaluate.
    * @param mixed $context
    * @return mixed
    */

    public function evaluate($context): mixed
    {
        return self::EvalBindingExpression($this->m_value, (array)$context); // "data";
    }
}
/**
 * 
 * @package IGK\Tests\System\Html\Dom
 * @author C.A.D. BONDJE DOUE
 */
class LoopHostTest extends BaseTestCase
{
    /**
     * 
     * @param string $content 
     * @return HtmlEvalExpression 
     */
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
        $n($data, null, (object)[
            'ctrl' => SysDbController::ctrl(),
            'raw' => [
                'x' => 44,
                'count' => 3
            ]
        ]);
        return $node->render();
    }

    public function test_loop_with_range()
    {
        $this->assertEquals(
            '<span><li>item 2</li></span><span><li>item 3</li></span><p>after 44</p>',
            $this->_loop_build([
                'span > loop(2..3)' => [
                    'li' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }

    /**
    * Tests loop with sub range.
    */
    public function test_loop_with_sub_range()
    {
        $this->assertEquals(
            '<span><ul><li>item 2</li></ul></span><span><ul><li>item 3</li></ul></span><p>after 44</p>',
            $this->_loop_build([
                'span > loop(2..3) > ul' => [
                    'li' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }

    /**
    * Tests loop complex.
    */
    public function test_loop_complex()
    {
        $this->assertEquals(
            '<span><ul><li>item = 2</li></ul></span><span><ul><li>item = 3</li></ul></span><li>select : {"x":44,"count":3}</li><span>item 0</span><span>item 1</span><span>item 2</span><p>after 44</p>',
            $this->_loop_build([
                'span' => [
                    'loop(2..3) > ul' => [
                        'li' => 'item = {{ $raw }}',
                    ],
                    ['li' => $this->evalExpression('select : {{ $raw }}')],
                    'loop([[:@raw->count]])' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }

    /**
    * Tests loop second complex loop.
    */
    public function test_loop_second_complex_loop()
    {
        $this->assertEquals(
            '<span><ul><div>subb : 2</div><span> child .... 0</span><span> child .... 1</span><span> child .... 2</span></ul></span><span><ul><div>subb : 3</div><span> child .... 0</span><span> child .... 1</span><span> child .... 2</span><span> child .... 3</span></ul></span><p>after 44</p>',
            $this->_loop_build([
                'span' => [
                    'loop(2..3) > ul' => [
                        'li' => [
                            'div' => 'subb : {{ $raw }}',
                            'loop($raw) > span' => ' child .... {{ $raw }}'
                        ],
                    ],
                    // ['li'=>$this->evalExpression('select : {{ $raw }}')],
                    // 'loop([[:@raw["count"]]])' => 'item {{ $raw }}'
                ],
                'p' => $this->evalExpression('after {{ $raw->x | json }}')
            ])
        );
    }

    /**
    * Tests loop html rendering.
    */
    public function test_loop_html_rendering()
    {
        $n = igk_create_node('div');
        $loop = $n->span()->setAttributes([
            'class' => 'indigo',
        ])->loop(range(1, 3));
        $loop->content = 'one';
        $s = $n->render();
        $this->assertEquals('<div><span class="indigo">one</span><span class="indigo">one</span><span class="indigo">one</span></div>',$s);
    }
}
