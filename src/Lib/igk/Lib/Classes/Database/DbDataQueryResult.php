<?php
// @file: IGKDataQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Database;

use IGKQueryResult;

final class DbDataQueryResult extends IGKQueryResult{
    const CREATE_ROW="obj://createrow";
    private $m_columns, $m_rows;
    ///<summary></summary>
    public function __construct(){
        $this->m_columns=array();
        $this->m_rows=array();
    }
    ///<summary></summary>
    ///<param name="tab"></param>
    public function addColumns($tab){
        foreach($tab as $k){
            $d=igk_createobj();
            $d->index=igk_count($this->m_columns);
            $d->name=$k;
            $this->m_columns[]=$d;
        }
    }
    ///<summary></summary>
    ///<param name="row"></param>
    public function addRow($row){
        $d=self::CREATE_ROW;
        if(is_object($row) && isset($row->$d) && ($row->$d == 1)){
            $this->m_rows[]=$row;
            unset($row->$d);
            return true;
        }
        $drow=$this->createRow();
        $row=is_object($row) ? (array)$row: $row;
        foreach($drow as $k=>$v){
            if(isset($row[$k])){
                $drow->$k=$row[$k];
            }
        }
        $this->m_rows[]=$drow;
    }
    ///<summary></summary>
    public function createRow(){
        $c=igk_createobj();
        foreach($this->m_columns as $v){
            $n=$v->name;
            $c->$n=null;
        }
        $d=self::CREATE_ROW;
        $c->$d=1;
        return $c;
    }
    ///<summary></summary>
    public function getColumns(){
        return $this->m_columns;
    }
    ///<summary></summary>
    public function getRowCount(){
        return igk_count($this->m_rows);
    }
    ///<summary></summary>
    public function getRows(){
        return $this->m_rows;
    }
}
