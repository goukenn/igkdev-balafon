<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaBuilderHelper.php
// @desc: schema builder helper
// @date: 20210422 06:53:24
namespace IGK\System\DataBase;

use Exception;
use IGK\Controllers\BaseController;
use IGK\Database\DbColumnInfo;
use IGK\System\Console\Logger;
use IGKException;

class SchemaBuilderHelper{
    protected $_output;
    protected $_schema;
    private $m_inf = [];

    public function getDefinition($n){
        return igk_getv($this->m_inf, $n);
    }
    protected function _addcolumnAttributes($attributes, $node=null){
        $node = $node ?? $this->_output;
        $c = new DbColumnInfo($attributes);
        $m = $node->add(IGK_COLUMN_TAGNAME);
        foreach($c as $k=>$v){
            $m[$k] = $v;
        } 
        $this->m_inf[$c->clName] = $c;
    }
    /**
     * migrate utility methods
     * @param mixed|array|object $options with 'migrations' fields. 
     * @return false 
     * @throws IGKException 
     */
    public static function Migrate($options, ?BaseController $controller = null){
        if ($m = igk_getv($options, "migrations")){ 
            try{
                foreach($m as $t){
                    if ($controller){ 
                        $t->controller = $controller;
                    }
                    $t->upgrade(); 
                }
            }
            catch(Exception $ex){
                Logger::danger(implode('\n', [__FILE__.":".__LINE__,"migrate error : " . $ex->getMessage()]));
            }
            return true;
        }
        return false;
    }
    /**
     * downgrade utility 
     * @param ixed|array|object $options with migrations fields. 
     * @return bool 
     * @throws IGKException 
     */
    public static function Downgrade( $options, ?BaseController $controller = null){
        if ($m = igk_getv($options, "migrations")){ 
            try{
                $m = array_reverse($m);
                foreach($m as $t){
                    if ($controller){ 
                        $t->controller = $controller;
                    }
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