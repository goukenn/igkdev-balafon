<?php

// @author: C.A.D. BONDJE DOUE
// @filename: EnvControllerInitControllers.php
// @date: 20221116 19:51:58
// @desc: 
namespace IGK\System\Caches;

use IGK\System\Exceptions\NotImplementException;

// + | --------------------------------------------------------------------
// + | 
// + |
class EnvControllerInitControllers{

    private $m_preload;

    private $m_dbcache;
    private $m_callback;
    private $m_adapter;
    private $m_table;
    private $m_resolvKey;

    public function __construct(EnvControllerCacheDataBase $dbcache, 
    callable $callback, array $controllers , string $ad_name, string $table, string $resolvKey)
    {
        $this->m_dbcache = $dbcache;
        $this->m_callback = $callback;
        $this->m_adapter = $ad_name;
        $this->m_table = $table;
        $this->m_preload = $controllers;
        $this->m_resolvKey = $resolvKey;
    }
    public function init(){
        $callback = $this->m_callback;
        while(count($this->m_preload) > 0 ){
            $q = array_shift($this->m_preload);
            $this->m_dbcache->loadDef($q, true);
            $b = $this->m_dbcache->getTableInfo(get_class($q), $this->m_adapter, $this->m_table);
            if ($b){
                $callback($b, $q, $this->m_resolvKey,  $this->m_table, $this->m_dbcache->getSerie());           
            } else {
                $callback($b, $q, $this->m_resolvKey,  null, $this->m_dbcache->getSerie()); 
            }
        }
    }
    public function resolv(){
        throw new NotImplementException(__METHOD__);
    }

  
}
