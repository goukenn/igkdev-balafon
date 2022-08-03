<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbConditionExpressionBuilder.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Database;

use IGK\System\Exceptions\ArgumentNotValidException;

/**
 * represent expression builder 
 * @package IGK\System\Database
 */
class DbConditionExpressionBuilder{
    var $operand = "AND";

    var $conditions = [];

    /**
     * call back expression
     * @var mixed
     */
    var $fc;

    public function __construct($operand)
    {
        if (!in_array($operand, explode("|", "OR|AND"))){
            throw new ArgumentNotValidException($$operand);
        }
        $this->operand = $operand;
    }
    /**
     * key must have 
     * @param mixed $key 
     * @param mixed $value 
     * @return $this 
     */
    public function add($key, $value){
        $this->conditions[] = [$key, $value];
        return $this;
    }  
}