<?php
// @file: IGKCSVQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
final class IGKCSVQueryResult extends IGKQueryResult{
    private $m_columns, $m_rowcount, $m_rows;
    /**
     * Constructor.
     */
    private function __construct(){    }
    /**
     * Returns the row at the given index.
     *
     * @param int $index Row index to retrieve.
     * @return mixed
     */
    public function getRowAtIndex(int $index) {
        return igk_getv($this->m_rows, $index);
    }
    /**
     * Indicates whether the query result was successful.
     *
     * @return bool
     */
    public function success(): bool {
        return true;
    }
    /**
     * Returns all rows as an array.
     *
     * @return array|null
     */
    public function to_array(): ?array {
        return $this->m_rows;
    }
    /**
     * Appends entries to the result set, optionally remapping column names.
     *
     * @param mixed      $e         Entries to append.
     * @param mixed|null $tableinfo Optional table column info for name remapping.
     * @return void
     */
    public function AppendEntries($e, $tableinfo=null){
        $this->m_rowcount += igk_count($e);
        if($tableinfo != null){
            foreach($e as $v){
                $t=array();
                foreach($v as $m=>$n){
                    $v_n=$tableinfo[$m];
                    $t[$v_n->clName]=$n;
                }
                $this->m_rows[]=(object)$t;
            }
        }
        else{
            foreach($e as $v){
                $this->m_rows[]=$v;
            }
        }
    }
    /**
     * Creates and returns an empty CSV query result instance.
     *
     * @param mixed|null $result      Unused result parameter.
     * @param mixed|null $seacharray  Unused search array parameter.
     * @return IGKCSVQueryResult
     */
    public static function CreateEmptyResult($result=null, $seacharray=null){
        $out=new IGKCSVQueryResult();
        $out->m_rowcount=0;
        $out->m_rows=array();
        return $out;
    }
    /**
     * Returns the column definitions for this result set.
     *
     * @return mixed
     */
    public function getColumns(){
        return $this->m_columns;
    }
    /**
     * Returns all rows in this result set.
     *
     * @return mixed
     */
    public function getRows(){
        return $this->m_rows;
    }
}