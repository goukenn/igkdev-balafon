<?php
// @author: C.A.D. BONDJE DOUE
// @file: RefColumnMapping.php
// @date: 20230124 20:34:04
namespace IGK\Database;
use ArrayIterator;
use IteratorAggregate;
use Traversable;
/**
* auto generate doc.
* @package IGK\Database
*/
/**
* auto generate doc.
* @package IGK\Database
*/
class RefColumnMapping implements IteratorAggregate{
    /**
    * Property: ref columns.
    * @var mixed
    */
    private $m_refColumns;
    /**
    * Property: data.
    * @var mixed
    */
    private $m_data;
    /**
    * auto generate doc.
    * @param array $columns column mapping definition. array<{real_colum=>mapping_column}|column>
    */
    public function __construct(array $data, array $columns){
        $this->m_data  = $data;
        $this->m_refColumns = $columns;
    }
    /**
    * Returns Alias.
    */
    public function getAlias(){
        $m = []; 
        $keys = array_keys($this->m_data);
        foreach($this->m_refColumns as $k=>$v){
            if (is_numeric($k)){
                $m[$v] = $v;
            }else{
                $m[$v] = $k;
            }
        }
        return $m;
    }
    /**
     * get iteraatr definitions 
     */
    public function getIterator(): Traversable { 
        $m = [];
        foreach($this->m_refColumns as $k=>$v){
            if (is_numeric($k)){
                $tab = explode('.', $v,2);
                $column_name = array_pop($tab);
                $m[$column_name] = igk_getv($this->m_data,$column_name);
            }else{
                $tab = explode('.', $k);
                $column_name = array_pop($tab);
                $m[$column_name] = igk_getv($this->m_data,  is_null($v) ? $column_name : $v);
            }
        }
        return new ArrayIterator($m); 
    }
    /**
    * check if isset innaccessible property
    * @param mixed $name
    */
    public function __isset($name)
    {
        if (isset($this->m_data[$name])){
            return true;
        }
    }
    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        $k = igk_getv($this->m_refColumns ,$name) ?? $name;
        return igk_getv($this->m_data, $k);
    }
}