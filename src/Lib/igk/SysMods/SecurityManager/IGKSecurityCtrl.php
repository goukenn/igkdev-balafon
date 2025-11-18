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
    * 
    */
    public function getConfigGroup(){
        return "administration";
    }
    /**
    * 
    */
    public function getConfigImageKey(){
        return "";
    }
    /**
    * 
    */
    public function getConfigPage(){
        return "security";
    }
    /**
    * 
    */
    public function getIsConfigPageAvailable(){
        return false;
    }
}
