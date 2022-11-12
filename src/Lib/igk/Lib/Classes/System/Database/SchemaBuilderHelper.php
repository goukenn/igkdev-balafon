<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaBuilderHelper.php
// @desc: schema builder helper
// @date: 20210422 06:53:24
namespace IGK\System\DataBase;

use Exception; 
use IGK\Database\DbColumnInfo;
use IGK\System\Console\Logger;
use IGKException;

class SchemaBuilderHelper{
    protected $_output;
    protected $_schema;

    protected function _addcolumnAttributes($attributes, $node=null){
        $node = $node ?? $this->_output;
        $c = new DbColumnInfo($attributes);
        $m = $node->add("Column");
        foreach($c as $k=>$v){
            $m[$k] = $v;
        }  
    }
    /**
     * migrate utility methods
     * @param mixed|array|object $options with migrations fields. 
     * @return false 
     * @throws IGKException 
     */
    public static function Migrate($options){
        if ($m = igk_getv($options, "migrations")){ 
            try{
                foreach($m as $t){
                    $t->upgrade(); 
                }
            }
            catch(Exception $ex){
                Logger::danger(implode('\n', [__FILE__.":".__LINE__,"migrate error : " . $ex->getMessage()]));
            }
        }
        return false;
    }
    /**
     * downgrade utility 
     * @param ixed|array|object $options with migrations fields. 
     * @return bool 
     * @throws IGKException 
     */
    public static function Downgrade( $options){
        if ($m = igk_getv($options, "migrations")){ 
            try{
                $m = array_reverse($m);
                foreach($m as $t){
                    $t->downgrade(); 
                }
                return true;
            }
            catch(Exception $ex){
                igk_dev_wln("\n",__FILE__.":".__LINE__." downgrade error : ", $ex->getMessage());
            }
        }
        return false;
    }
    
}