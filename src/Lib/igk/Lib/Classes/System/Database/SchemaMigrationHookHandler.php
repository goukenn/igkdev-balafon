<?php
// @author: C.A.D. BONDJE DOUE
// @file: SchemaMigrationHookHandler.php
// @date: 20230129 16:13:18
namespace IGK\System\Database;

use IGK\System\Console\Logger;
use IGKEvents;

///<summary></summary>
/**
* 
* @package IGK\System\Database
*/
class SchemaMigrationHookHandler{
    private $m_hooks = [];
    public $controller;
    public $tables;
    private  $m_Links;
    const onColumnRename = 'onColumnRename';
    private function initHooks(){
        $this->m_hooks[self::onColumnRename] = function($e){
            extract($e->args);
            $this->onColumnRenamed($table, $column, $new_name); 
        };
    }
    public function register(){
         $this->initHooks();
        igk_reg_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, $this->m_hooks[self::onColumnRename]);
    }
    public function unregister(){
        igk_unreg_hook(IGKEvents::HOOK_DB_RENAME_COLUMN, $this->m_hooks[self::onColumnRename]);
    }

    protected function onColumnRenamed($table, $column, $name){
        // Logger::print('column rename : '.$column , $name); 
        /**
         * load links 
         */
        if (is_null($this->m_Links)){

            $Links = [];
            foreach($this->tables as $tb=>$v){
                foreach($v->columnInfo as $cl){
                    if ($lk = $cl->clLinkType){
                        $id = $cl->clLinkColumn ?? IGK_FD_ID;
                        $key = $lk.'.'.$id;
                        if (!isset($Links[$key])){
                            $Links[$key] = [];
                        }
                        $Links[$key][]= (object)['table'=>$tb, 'id'=>$id, 'column'=>$cl->clName];
                    }
                }
            }
            $this->m_Links = $Links;
        }

        $n_sk = sprintf('%s.%s', $table, $name);
        if ($cl = igk_getv($this->m_Links, $n_sk)){
            // update column fields
            foreach($cl as $r){
                // update column fields
                $this->tables[$r->table][$r->column]->clLinkColumn = $name;
            }
        }

        $sk = sprintf('%s.%s', $table, $column);
        if ($cl = igk_getv($this->m_Links, $sk)){
            // update column fields
            foreach($cl as $r){
                // update column fields
                $tb = $this->tables[$r->table]->columnInfo;

                $tb[$r->column]->clLinkColumn = $name;
            }
            igk_array_replace_key($this->m_Links, $sk, $n_sk, $cl );
        }
 
    }
}