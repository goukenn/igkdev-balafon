<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ReferenceModelController.php
// @date: 20220311 15:59:05
// @desc: reference model controller

namespace IGK\Controllers;

///<summary> used for referencing global value data</summary>
/**
*  used for referencing global value data
*/
final class ReferenceModelController extends NonVisibleControllerBase{
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="model" default="null"></param>
    ///<param name="base" default="36"></param>
    ///<param name="ref" default="6"></param>
    /**
    * 
    * @param mixed $ctrl
    * @param mixed $model the default value is null
    * @param mixed $base the default value is 36
    * @param mixed $ref the default value is 6
    */
    public function get_ref($ctrl, $model=null, $base=36, $ref=6){
        return \IGK\Models\ReferenceModels::get_ref($ctrl, $model, $base, $ref); 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDataTableInfo(){
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDataTableName(){
        return \IGK\Models\ReferenceModels::table(); 
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getName(){
        return IGK_CB_REF_CTRL;
    }
    ///<summary></summary>
    ///<param name="t"></param>
    ///<param name="productTypeTable"></param>
    ///<param name="prefix" default="null"></param>
    /**
    * 
    * @param mixed $t
    * @param mixed $productTypeTable
    * @param mixed $prefix the default value is null
    */
    public function getnewproduct_ref($t, $productTypeTable, $prefix=null){
        $r=igk_db_table_select_where($productTypeTable, array(IGK_FD_ID=>$t->clProductType_Id));
        $row=$r->getRowAtIndex(0);
        if($row == null){
            return null;
        }
        $n=igk_getv($row, IGK_FD_NAME);
        $v_tmodel=($prefix ? $prefix: igk_configs()->Prefix).$row->clPrefix;
        $r=igk_db_table_select_where($this->getDataTableName(), array("clModel"=>$v_tmodel));
        $model=$r->RowCount == 0 ? 0: $r->getRowAtIndex(0);
        $c=$model ? $model->clNextValue: null;
        $c++;
        $out=$v_tmodel."".IGKNumber::ToBase($c, 36, 6);
        return IGKRefoutModel::Init(array(
            "out"=>$out,
            "ctrl"=>$this,
            "clModel"=>$v_tmodel,
            "clNextValue"=>$c,
            IGK_FD_ID=>$model ? $model->clId: null
        ));
    }
}