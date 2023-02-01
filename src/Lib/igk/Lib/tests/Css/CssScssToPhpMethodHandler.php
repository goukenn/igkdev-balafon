<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssScssToPhpMethodHandler.php
// @date: 20230125 18:08:59
namespace IGK\Tests\Css;

use IGK\Tests\BaseTestCase;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\Tests\Css
*/
class CssScssToPhpMethodHandler extends BaseTestCase{

    /**
     * expression list
     * @var ?array<string> expression
     */
    private $m_expressions = [];
    /**
     * evaluate arguments
     * @param string $key 
     * @param mixed $argument 
     * @return mixed 
     * @throws IGKException 
     */
    public function eval(string $key, $arguments){
        if ($fc = igk_getv($this->m_expressions, $key)){
            return $this->_invoke($fc, $arguments);
        }
    }
    /**
     * evaluate loaded expression 
     * @return mixed 
     */
    private function _invoke(){
        extract(func_get_arg(1));
        return @eval(func_get_arg(0));
    }
}