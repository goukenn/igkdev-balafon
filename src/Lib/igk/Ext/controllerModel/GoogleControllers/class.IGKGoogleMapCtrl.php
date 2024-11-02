<?php
// @file: class.IGKGoogleMapCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

///<summary>Represente class: IGKGoogleMapCtrl</summary>

use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

/**
* Represente IGKGoogleMapCtrl class
*/
abstract class IGKGoogleMapCtrl extends \IGK\Controllers\ControllerTypeBase {
    ///<summary></summary>
    /**
    * 
    */
    public static function GetAdditionalConfigInfo(){
        return array(
            "clGoogleMapUrl",
            igk_create_additional_config_info(array("clRequire"=>1))
        );
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getcanAddChild(){
        return false;
    }
    ///<summary></summary>
    ///<param name="t" ref="true"></param>
    /**
    * 
    * @param  * $t
    */
    public static function SetAdditionalConfigInfo(& $t){
        $t["clGoogleMapUrl"]=igk_getr("clGoogleMapUrl");
    }
    ///<summary></summary>
    /**
    * 
    */
    public function View():BaseController{
        $t=$this->TargetNode;
        $t->clearChilds();
        $lnk=igk_getv($this->Configs, "clGoogleMapUrl", "http://www.google.fr");
        $s=<<<EOF
<iframe class="noborder googlemap_map" src="{$lnk}"></iframe>
EOF;
        $t->Load($s);
        return $this;
    }
}
