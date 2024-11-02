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

    const OP_AND = "AND";
    const OP_OR = "OR";

    /**
     * call back expression
     * @var mixed
     */
    var $fc;

    /**
     * 
     * @param mixed $operand 
     * @return void 
     * @throws ArgumentNotValidException 
     */
    public function __construct($operand = self::OP_AND)
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
    public function __toString()
    {
        return implode($this->operand == self::OP_AND ? ' AND ': ' OR ', array_map(function($v){
            return $v[0].'=??';
        }, $this->conditions));
    }
    /**
     * create operand
     * @param mixed $conditions 
     * @param mixed $operand 
     * @return static 
     */
    public static function Create($conditions, $operand = self::OP_AND){
        $s = new static($operand);
        foreach($conditions as $k=>$v){
            $s->add($k, $v);
        }
        return $s;
    }
}