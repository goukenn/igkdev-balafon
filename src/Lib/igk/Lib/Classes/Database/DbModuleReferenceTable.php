<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbModuleReferenceTable.php
// @date: 20221116 12:24:15
namespace IGK\Database;

use ArrayAccess;
use IDbGetTableReferenceHandler;
use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* 
* @package IGK\Database
*/
class DbModuleReferenceTable implements ArrayAccess{
    use ArrayAccessSelfTrait;
    private $m_tabledef;
    private $m_controller;
    private $m_source;
    private $m_request_changed = [];
    public function __construct(IDbGetTableReferenceHandler $controller, array $tables, array $source)
    {
        $this->m_tabledef = $tables;
        $this->m_controller = $controller;
        $this->m_source =  $source;
    }
    /**
     * update reference
     * @return array 
     */
    public function udpate(){              
        if ($rc = $this->m_request_changed){
            foreach($rc as $v){
                $v->tableRowReference = $v->columnInfo;
            }
        }
        return $this->m_source;
    }
    public function getTableDefinition(){
        return $this->m_tabledef;
    }
    public function _access_offsetGet($n){
        if (key_exists($n, $this->m_tabledef)){
            return $this->m_tabledef[$n];
        }
        // possibility of definition in global system 
        /** load only definition without altering the table */
        $table = $this->m_controller->resolvTableDefinition($n);
      
        if (is_null($table) || is_array($table)){
            igk_wln_e(__FILE__.":".__LINE__,  "global table not found ",$n, $table);
        }
        // $host = $this->m_controller->getHost();
        // $hostname = $host instanceof ApplicationModuleController ? $host->getName() : 
        //     get_class($host);
        $this->m_request_changed[$n] = & $table;
        /// TODO:: attache to columns info 
        // $inf = & $this->m_request_changed[$n]->columnInfo;
        // $tv = igk_getv($inf, 'clIsUsedBy');
        // if (!empty($tv)){
        //     $inf['clIsUserBy'] =  igk_array_unique_string(',', $tv, $host);
        //    // $inf['clIsUserBy'] = $hostname;
        // }else 
        //     $inf['clIsUserBy'] = 'hostname';
        // igk_setv($inf, 'clIsUsedBy', $hostname );
        //$this->m_request_changed[$n]->columnInfo->clIsUsedBy = $hostname;
        return $table;
        
    }
}


