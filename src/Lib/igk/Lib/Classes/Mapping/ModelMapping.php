<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelMapping.php
// @date: 20230117 14:15:41
namespace IGK\Mapping;

use IGK\Helper\StringUtility;
use IGK\Models\ModelBase;

/**
 * used to map data to model
 * @package com\igkdev\app\llvGStock\Actions
 */
class ModelMapping{
    /**
     * prefix of field if non strict mapping
     * @var null|string
     */
    var $prefix;
    /**
     * model to use
     * @var ModelBase
     */
    var $model;
    /**
     * mapping keys [data_key => model_key]. for strict mapping
     * @var ?array
     */
    var $mapping;
    
    public function __construct(\IGK\Models\ModelBase $model, ?string $prefix){
        $this->model = $model;
        $this->prefix = $prefix;
    }
    /**
     * map data
     * @param mixed $data 
     * @param mixed $ob object to bind 
     * @return object|bool 
     */
    public function map($data, $ob=null){
        $keys = array_fill_keys($this->model->colKeys(), 1); 
        $result = false;
        $m = is_null($ob) ? 1 : 0;
        $resolv = [];
        if (is_object($data)){
            $ob = $ob ?? (object)[];
            foreach($data as $k=>$v){
                if (is_object($v))continue;

                if ($this->mapping){
                    if (isset($this->mapping[$k])){
                        $q = $this->mapping[$k];
                        $ob->$q = $v;
                        $result = true;
                        $resolv[$q] = 1;
                    }
                    continue;
                }   
                $nk = [];
                $nk[] = $this->prefix.$k;
                $nk[] = $this->prefix.igk_str_snake($k);
                $nk[] = $this->prefix.StringUtility::CamelClassName($k);
                $nk[] = $k;
                while(count($nk)>0){
                    $q = array_shift($nk);
                    if (!isset($resolv[$q]) && isset($keys[$q])){                    
                        $ob->$q = $v;
                        $result = true;
                        $resolv[$q] = 1;
                        break;
                    }
                }
            }
            return $m == 1? $ob : $result;
        }
        return $result;
    }
}