<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbModuleReferenceTable.php
// @date: 20221116 12:24:15
namespace IGK\Database;
use ArrayAccess;

use IGK\Controllers\ApplicationModuleController;
use IGK\Controllers\BaseController;
use IGK\IDbGetTableReferenceHandler;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* 
* @package IGK\Database
*/
class DbModuleReferenceTable implements ArrayAccess{
    use ArrayAccessSelfTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_tabledef;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_controller;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_source;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_request_changed = [];

    /**
    * .ctr
    * @param IDbGetTableReferenceHandler $controller
    * @param array $tables
    * @param array $source
    */
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
    /**
     * get table reference definition
     * @return null|array 
     */

    public function & getRefTableDefinition():?array{
        return  $this->m_tabledef;
    }

    /**
    * auto generate doc.
    */
    public function getTableDefinition(){
        return $this->m_tabledef;
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
    public function _access_offsetExists($n){
        return key_exists($n, $this->m_tabledef);
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */
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
        return $table;
    }
}