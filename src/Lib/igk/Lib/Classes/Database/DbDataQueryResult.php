<?php
// @file: IGKDataQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
use IGKQueryResult;

/**
* Db data query result.
* @package IGK\Database
*/
final class DbDataQueryResult extends IGKQueryResult{
    /**
    * Constant: create row.
    * @var mixed
    */
    const CREATE_ROW="obj://createrow";
    /**
    * Properties: columns, rows.
    * @var mixed
    */
    private $m_columns, $m_rows;
    /**
    * .ctr
    */
    public function __construct(){
        $this->m_columns=array();
        $this->m_rows=array();
    }
    /**
    * To array.
    * @return ?array
    */
    public function to_array(): ?array {
        return $this->m_rows;
    }
    /**
    * Returns Row At Index.
    * @param mixed $index
    */
    public function getRowAtIndex($index){
        return igk_getv($this->m_rows, $index);
    }
    /**
    * Success.
    * @return bool
    */
    public function success(): bool
    {
        return true;
    }
    /**
    * Adds Columns.
    * @param mixed $tab
    */
    public function addColumns($tab){
        foreach($tab as $k){
            $d=igk_createobj();
            $d->index=igk_count($this->m_columns);
            $d->name=$k;
            $this->m_columns[]=$d;
        }
    }
    /**
    * Adds Row.
    * @param mixed $row
    */
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
    /**
    * Creates Row.
    */
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
    /**
    * Returns Columns.
    */
    public function getColumns(){
        return $this->m_columns;
    }
    /**
    * Returns Row Count.
    */
    public function getRowCount(){
        return igk_count($this->m_rows);
    }
    /**
    * Returns Rows.
    */
    public function getRows(){
        return $this->m_rows;
    }
}