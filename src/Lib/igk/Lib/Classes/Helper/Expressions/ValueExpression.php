<?php
// @author: C.A.D. BONDJE DOUE
// @file: ValueExpression.php
// @date: 20230225 19:25:21
namespace IGK\Helper\Expressions;

/**
* auto generate doc.
* @package IGK\Helper\Expressions
*/
class ValueExpression{

    /**
    * Property: data.
    * @var mixed
    */
    protected $data = [];
    /**
     * replace 
     * @param string $data 
     * @return string 
     */

    public function replace(string $data){
        foreach($this->data as $k=>$v){
            $data = str_replace($k, $v, $data);
        }
        return $data;
    }
}