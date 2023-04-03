<?php

namespace IGK\Ext\Adapters\SQLite3;

use IGK\Database\DbQueryResult;

class SQLite3Result extends DbQueryResult{
    private $m_result ;
    private $m_info;
    private $m_query;
    private $m_columns;
    private $m_fetch =false;
    private function __construct()
    {
        
    }

    public function success(): bool { 
        return true;
    }

    public function to_array(): ?array {
        return $this->getRows();
     }
    public static function CreateResult($result, $query, $info){
        $ri = new self;
        $ri->m_result = $result;
        $ri->m_query = $query;
        $ri->m_info = $info;
        $ri->m_result->Columns = $ri->getColumns();       
        return $ri;
    }
    public function getRows(){
        return $this->m_result->Rows;
    }
    public function getRowAtIndex(int $index)
    {
        if (!$this->m_fetch){
            $this->fetch();
        }
        if (count($this->m_result->Rows) < $index){
            while(($index > 0) && ($d = $this->fetch())){            
                $index--;
            }
            return $d;
        }
        return igk_getv($this->m_result->Rows, $index);
    }
    public function fetch_all(){
        $res = $this->m_result->res;
         // fech all 
         while($this->fetch());
         return $this->m_result->Rows;
    }
    public function fetch(){
        $this->m_fetch = true;
        $res = $this->m_result->res;
        $b = igk_db_fetch_assoc($res);
        if ($b){
            $this->m_result->Rows[] = $b;
        }
        return $b;
    }
    public function getColumns(){
        $res = $this->m_result->res;
        if (is_null($this->m_columns)){
            $g = igk_db_num_fields($res);
            $tb = [];
            while($g > 0){
                $cl = igk_sql3lite_fetch_field($res);
                $g--;
                if ($cl){
                    $tb[$cl->name] = $cl;
                }
            }
            $this->m_columns = (object)$tb;
        }
        return $this->m_columns;
    }
}