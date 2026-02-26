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
    * auto generate doc.
    * @var mixed
    */
    private $m_expression;

    /**
    * auto generate doc.
    */
    public function getValue(){
        return $this->m_expression;
    }

    /**
    * auto generate doc.
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