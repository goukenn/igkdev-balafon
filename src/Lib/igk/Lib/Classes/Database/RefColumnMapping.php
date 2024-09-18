<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefColumnMapping.php
// @date: 20230124 20:34:04
namespace IGK\Database;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

///<summary></summary>
/**
* 
* @package IGK\Database
*/
class RefColumnMapping implements IteratorAggregate{
    private $m_refColumns;
    private $m_data;
    public function __construct(array $data, array $columns){
        $this->m_data  = $data;
        $this->m_refColumns = $columns;
    }
    public function getAlias(){
        $m = []; 
        foreach($this->m_refColumns as $k=>$v){
            if (is_numeric($k)){
                $m[$v] = $v;
            }else{
                $m[$v] = $k;
            }
        }
        return $m;
    }

    public function getIterator(): Traversable { 
        $m = [];
        foreach($this->m_refColumns as $k=>$v){



            if (is_numeric($k)){
                $m[$v] = igk_getv($this->m_data,$v);
            }else{
                $tab = explode('.', $k);
                $column_name = array_pop($tab);
                $m[$column_name] = igk_getv($this->m_data, $v);
            }
        }
        return new ArrayIterator($m); // this->m_data);
    }
    public function __isset($name)
    {
        if (isset($this->m_data[$name])){
            return true;
        }
    }
    public function __get($name){
        $k = igk_getv($this->m_refColumns ,$name) ?? $name;
        return igk_getv($this->m_data, $k);
    }
}