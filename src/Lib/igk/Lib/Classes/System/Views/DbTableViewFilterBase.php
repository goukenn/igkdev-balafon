<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DbTableViewFilterBase.php
// @date: 20220704 11:25:47
// @desc: 

namespace IGK\System\Views;


abstract class DbTableViewFilterBase implements IDbTableViewFilter{
    public function getHeaderList($firstRow){
        return array_keys($firstRow); 
    }
    public function filter($key, $value, $node){
        if (method_exists($this, $fc = "filter_".$key)){
            $this->$fc($value, $node);
        }else{
            $node->Content = $value;
        }
    }
}