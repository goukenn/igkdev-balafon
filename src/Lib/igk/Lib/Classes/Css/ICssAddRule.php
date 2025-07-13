<?php
// @author: C.A.D. BONDJE DOUE
// @file: ICssAddRule.php
// @date: 20250228 10:24:17
namespace IGK\Css;
/**
* 
* @package IGK\Css
* @author C.A.D. BONDJE DOUE
*/
interface ICssAddRule{
    /**
     * add css rule
     * @param string $name 
     * @param string $expression 
     * @return mixed 
     */
    function addRule(string $name, string $expression);
}