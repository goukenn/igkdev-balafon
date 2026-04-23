<?php
// @file: igk.security.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Configuration\Controllers\ConfigControllerBase;

/**
* represent a controller to manage security
*/
class IGKSecurityCtrl extends ConfigControllerBase{
    /**
    * auto generate doc.
    */    public function getConfigGroup(){
        return "administration";
    }
    /**
    * auto generate doc.
    */    public function getConfigImageKey(){
        return "";
    }
    /**
    * auto generate doc.
    */    public function getConfigPage(){
        return "security";
    }
    /**
    * auto generate doc.
    */    public function getIsConfigPageAvailable(){
        return false;
    }
}