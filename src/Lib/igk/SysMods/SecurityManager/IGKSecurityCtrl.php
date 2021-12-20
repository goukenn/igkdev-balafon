<?php
// @file: igk.security.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

///<summary>represent a controller to manage security</summary>

use IGK\System\Configuration\Controllers\ConfigControllerBase;

/**
* represent a controller to manage security
*/
class IGKSecurityCtrl extends ConfigControllerBase{
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigGroup(){
        return "administration";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigImageKey(){
        return "";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getConfigPage(){
        return "security";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getIsConfigPageAvailable(){
        return false;
    }
}
