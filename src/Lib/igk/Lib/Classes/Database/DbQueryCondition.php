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
    /**
    * Property: row.
    * @var mixed
    */
    private $m_row;
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * Property: operand.
    * @var mixed
    */
    var $operand = 'AND';
    /**
    * Constant: op and.
    * @var mixed
    */
    const OP_AND = 'AND';
    /**
    * Constant: op or.
    * @var mixed
    */
    const OP_OR = 'OR';
    /**
     * association query array 
     * @param array $data 
     * @return void 
     */
    public function set(?array $data){
        $this->m_data = $data;
        $this->m_row = (object)array_fill_keys (array_keys($data), null);
    }
    /**
    * auto generate doc.
    * @param mixed $obj
    * @param mixed $operand
    * @return void
    */
    private function __construct($obj, $operand='AND')
    {
        $this->m_row = $obj;
        $this->m_data = [];
        $this->operand = $operand;
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        return igk_getv($this->m_row, $n);
    }
    /**
    * setter
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){ 
        $pk = ltrim($n, "@!<=>");
        if (property_exists($this->m_row, $pk)){
            $this->m_data[$n] = $v;
        } else {
            if (igk_environment()->isDev()){
                igk_die("property ".$pk . " not found");
            }
        }
        $this->m_row->$n = $v;
    }
    /**
    * To array.
    */
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
    * auto generate doc.
    * @param array $list
    * @param mixed $operand
    * @return static
    */
    public static function Create(array $list, $operand = self::OP_AND){
        $s = new static((object)array_fill_keys (array_keys($list), null));
        $s->m_data = $list; 
        $s->operand = $operand;
        return $s;
    }
}