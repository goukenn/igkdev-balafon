<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlLoadingContextOptions.php
// @date: 20221010 13:26:14
namespace IGK\System\Html;


///<summary></summary>
/**
* 
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
    var $transformToEval;

    /**
     * expression to use in hook - loop 
     * @var mixed
     */
    var $hookExpression;
}