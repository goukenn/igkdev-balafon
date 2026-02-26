<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbQueryExpression.php
// @date: 20250618 09:53:28
namespace IGK\System\Database;
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
class DbQueryExpression{

    /**
    * Property: expression.
    * @var mixed
    */
    private $m_expression;

    /**
    * Returns Value.
    */
    public function getValue(){
        return $this->m_expression;
    }

    /**
    * Sets Value.
    * @param string $v
    */
    protected function setValue(string $v){
        $this->m_expression = $v;
    }
    private function __construct(){
    }
    /**
     * create a DbExpression
     * @param string $expression 
     * @return static 
     */

    public static function Create(string $expression){
        $x = new static;
        $x->m_expression = $expression;
        return $x;
    }
}