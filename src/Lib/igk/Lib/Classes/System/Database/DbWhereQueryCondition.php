<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbWhereQueryCondition.php
// @date: 20241013 15:05:38
namespace IGK\System\Database;


///<summary></summary>
/**
* 
* @package IGK\System\Database
* @author C.A.D. BONDJE DOUE
*/
class DbWhereQueryCondition implements IDbWhereQueryCondition{
    var $operand = self::AND_OP;
    var $conditions = [];
    const AND_OP = 'AND';
    const OR_OP = 'OR';
    protected function __construct()
    {
        
    }
    public static function Create($conditions, string $operand=self::AND_OP){
        $a = new static;
        $a->conditions = $conditions;
        $a->operand = in_array($operand, [self::AND_OP, self::OR_OP]) ? $operand : self::AND_OP;
        return $a;
    }
    public function getConditionInfo(): array { 
        return [$this->operand, $this->conditions];
    }

}