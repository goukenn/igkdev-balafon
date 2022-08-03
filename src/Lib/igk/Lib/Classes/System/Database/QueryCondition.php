<?php
// @author: C.A.D. BONDJE DOUE
// @filename: QueryCondition.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Database;

///<summary> represent a query condition</summary>
class QueryCondition{
    /**
     * operant type
     * @var mixed
     */
    var $operand;
    /**
     * array of conditions
     */
    var $conditions;

    protected function __construct()
    {        
    }

    public static function Create(array $conditions, $operand="AND"){
        if (!in_array($operand, ["OR", "AND"])){
            die("not a valid operand");
        }
        $c = new static;
        $c->operand = $operand;
        $c->$conditions = $conditions;
        return $c;
    }
}