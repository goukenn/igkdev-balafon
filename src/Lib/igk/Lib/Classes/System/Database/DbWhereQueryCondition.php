<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbWhereQueryCondition.php
// @date: 20241013 15:05:38
namespace IGK\System\Database;
/**
* auto generate doc.
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
class DbWhereQueryCondition implements IDbWhereQueryCondition{
    /**
    * Property: operand.
    * @var mixed
    */
    var $operand = self::AND_OP;
    /**
    * Property: conditions.
    * @var mixed
    */
    var $conditions = [];
    /**
    * Constant: and op.
    * @var mixed
    */
    const AND_OP = 'AND';
    /**
    * Constant: or op.
    * @var mixed
    */
    const OR_OP = 'OR';
    /**
    * .ctr
    */
    protected function __construct()
    {
    }
    /**
    * Creates.
    * @param mixed $conditions
    * @param string $operand
    */
    public static function Create($conditions, string $operand=self::AND_OP){
        $a = new static;
        $a->conditions = $conditions;
        $a->operand = in_array($operand, [self::AND_OP, self::OR_OP]) ? $operand : self::AND_OP;
        return $a;
    }
    /**
    * Returns Condition Info.
    * @return array
    */
    public function getConditionInfo(): array { 
        return [$this->operand, $this->conditions];
    }
}