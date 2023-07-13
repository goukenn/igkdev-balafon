<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbQueryCondition.php
// @date: 20220628 15:18:02
namespace IGK\Database;


///<summary></summary>
/**
* create a query condition 
* @package IGK\Database
*/
class DbQueryCondition{
    private $row;
    private $data;
    var $operand = 'AND';
    /**
     * association query array 
     * @param array $data 
     * @return void 
     */
    public function set(?array $data){
        $this->data = $data;
    }
    public function __construct($obj)
    {
        $this->row = $obj;
        $this->data = [];
    }
    public function __get($n){
        return igk_getv($this->row, $n);
    }
    public function __set($n, $v){ 
        $pk = ltrim($n, "@!<=>");
        if (property_exists($this->row, $pk)){
            $this->data[$n] = $v;
        } else {
            if (igk_environment()->isDev()){
                igk_die("property ".$pk . " not found");
            }
        }
        $this->row->$n = $v;
    }
    public function to_array(){
        return $this->data;
    }
    public function __call($n, $arguments){
        $this->__set($n, $arguments[0]);
        return $this;
    }
}