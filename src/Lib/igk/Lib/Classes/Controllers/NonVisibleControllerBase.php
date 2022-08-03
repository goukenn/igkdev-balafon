<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NonVisibleControllerBase.php
// @date: 20220803 13:48:58
// @desc: 

namespace IGK\Controllers;

use IGK\Controllers\BaseController;
use IGKException;

///<summary>Represente class: NonVisibleControllerBase</summary>
/**
* Represente NonVisibleControllerBase class
*/
abstract class NonVisibleControllerBase extends BaseController {
    public function getDataTableName(){ 
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getCanAddChild(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getcanDelete(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getcanModify(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsVisible(){
        return false;
    }
    ///<summary></summary>
    /**
    * 
    */
    protected function initTargetNode(){
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function View(){
        throw new IGKException("Not implement");
    }
    public function IsFunctionExposed($func){        
        return igk_is_conf_connected();
    }
}