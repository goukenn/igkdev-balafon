<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbQueryCondition.php
// @date: 20220628 15:18:02
namespace IGK\Database;
use Exception;
/**
* create a query condition : will check that property exists before create chaining. 
* @package IGK\Database
*/
class DbQueryCondition{
    private $row;
    private $m_data;
    var $operand = 'AND';
    const OP_AND = 'AND';
    const OP_OR = 'OR';
    /**
     * association query array 
     * @param array $data 
     * @return void 
     */
    public function set(?array $data){
        $this->m_data = $data;
    }
    /**
     * 
     * @param mixed $obj row definition property
     * @param 'OR'|'AND' $operand tag 
     * @return void 
     */
    public function __construct($obj, $operand='AND')
    {
        $this->row = $obj;
        $this->m_data = [];
        $this->operand = $operand;
    }
    public function __get($n){
        return igk_getv($this->row, $n);
    }
    public function __set($n, $v){ 
        $pk = ltrim($n, "@!<=>");
        if (property_exists($this->row, $pk)){
            $this->m_data[$n] = $v;
        } else {
            if (igk_environment()->isDev()){
                igk_die("property ".$pk . " not found");
            }
        }
        $this->row->$n = $v;
    }
    public function to_array(){
        return $this->m_data;
    }
    /**
     * everery method call set the property 
     * @param mixed $n 
     * @param mixed $arguments 
     * @return $this 
     * @throws Exception 
     */
    public function __call($n, $arguments){
        $this->__set($n, $arguments[0]);
        return $this;
    }
    /**
     * 
     * @param array $list 
     * @return static 
     */
    public static function Create(array $list, $operand = self::OP_AND){
        $s = new static((object)array_fill_keys (array_keys($list), null));
        $s->m_data = $list; 
        $s->operand = $operand;
        return $s;
    }
}