<?php
// @file: IGKCSVQueryResult.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

final class IGKCSVQueryResult extends IGKQueryResult{
    private $m_columns, $m_rowcount, $m_rows;
    ///<summary></summary>
    private function __construct(){    }

    public function success(): bool { 
        return true;
    }

    public function to_array(): ?array { 
        return $this->m_rows;
    }
    ///<summary></summary>
    ///<param name="e"></param>
    ///<param name="tableinfo" default="null"></param>
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
    ///<summary></summary>
    ///<param name="result" default="null"></param>
    ///<param name="seacharray" default="null"></param>
    public static function CreateEmptyResult($result=null, $seacharray=null){
        $out=new IGKCSVQueryResult();
        $out->m_rowcount=0;
        $out->m_rows=array();
        return $out;
    }
    ///<summary></summary>
    public function getColumns(){
        return $this->m_columns;
    }
    ///<summary></summary>
    public function getRows(){
        return $this->m_rows;
    }
}
