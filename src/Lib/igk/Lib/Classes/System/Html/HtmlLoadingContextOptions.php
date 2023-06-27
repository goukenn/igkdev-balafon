<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlLoadingContextOptions.php
// @date: 20221010 13:26:14
namespace IGK\System\Html;


///<summary></summary>
/**
* use with HtmlReader to set object context 
* @package IGK\System\Html
*/
class HtmlLoadingContextOptions{
    /**
     * controller
     * @var mixed
     */
    var $ctrl;
 
    /**
     * raw data
     * @var mixed
     */
    var $raw;

    /**
     * in loading expression
     * @var bool 
     */
    var $load_expression;

    /**
     * transform to eval
     * @var bool
     */
    var $transformToEval = false;

    /**
     * expression to use in hook - loop 
     * @var mixed
     */
    var $hookExpression;

    /**
     * engine node
     * @var ?HtmlNode
     */
    var $engineNode;

    /**
     * disable expression interpolation on loading
     * @var bool 
     */
    var $noInterpolation = false;

    public function __toString()
    {
        return static::class;
    }
    public function __get($name){
        igk_trace();
        igk_wln_e("try = ".$name);
    }
}