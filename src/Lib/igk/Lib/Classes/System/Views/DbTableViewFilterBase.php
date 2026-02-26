<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbTableViewFilterBase.php
// @date: 20220704 11:25:47
// @desc: 
namespace IGK\System\Views;

/**
* Db table view filter base.
* @package IGK\System\Views
*/
abstract class DbTableViewFilterBase implements IDbTableViewFilter{

    /**
    * Returns Header List.
    * @param mixed $firstRow
    */
    public function getHeaderList($firstRow){
        return array_keys($firstRow); 
    }

    /**
    * Filters.
    * @param mixed $key
    * @param mixed $value
    * @param mixed $node
    */
    public function filter($key, $value, $node){
        if (method_exists($this, $fc = "filter_".$key)){
            $this->$fc($value, $node);
        }else{
            $node->Content = $value;
        }
    }
}