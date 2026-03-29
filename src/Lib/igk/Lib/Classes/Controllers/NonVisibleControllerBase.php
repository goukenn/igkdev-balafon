<?php
// @author: C.A.D. BONDJE DOUE
// @filename: NonVisibleControllerBase.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Controllers;
use IGK\Controllers\BaseController;
use IGK\System\Controllers\Traits\NoDbActiveControllerTrait;
use IGKException;
/**
* Represent NonVisibleControllerBase class
*/
abstract class NonVisibleControllerBase extends BaseController {
    use NoDbActiveControllerTrait;
    /**
    * auto generate doc.
    */    public function getCanAddChild(){
        return false;
    }
    /**
    * auto generate doc.
    */    public function getcanDelete(){
        return false;
    }
    /**
    * auto generate doc.
    */    public function getcanModify(){
        return false;
    }
    /**
    * auto generate doc.
    */    public function getIsVisible():bool{
        return false;
    }
    /**
    * auto generate doc.
    */    protected function initTargetNode(): ?\IGK\System\Html\Dom\HtmlNode{
        return null;
    }
    /**
    * auto generate doc.
    */    public function View():BaseController{
        throw new IGKException("Not implement");
    }
    /**
    * Returns true if Function Exposed.
    * @param mixed $func
    */
    public function IsFunctionExposed($func){        
        return igk_is_conf_connected();
    }
}