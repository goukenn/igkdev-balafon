<?php
namespace IGK\System\Database;

use IGK\System\Exceptions\ArgumentNotValidException;

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

    public function add($key, $value){
        array_push($this->conditions, [$key, $value]);
        return $this;
    }
}