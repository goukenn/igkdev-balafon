<?php
// @file: class.IGKGoogleMapCtrl.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Ext\Controllers\Google;
use IGK\Controllers\BaseController;
use IGK\System\Html\Dom\HtmlNode;

/**
* Represent IGKGoogleMapCtrl class
*/
abstract class IGKGoogleMapCtrl extends \IGK\Controllers\ControllerTypeBase {
    /**
    * auto generate doc.
    */    public static function GetAdditionalConfigInfo(){
        return array(
            "clGoogleMapUrl",
            igk_create_additional_config_info(array("clRequire"=>1))
        );
    }
    /**
    * auto generate doc.
    */    public function getcanAddChild(){
        return false;
    }
    /**
    * auto generate doc.
    * @param mixed & $t
    */
    public static function SetAdditionalConfigInfo(& $t){
        $t["clGoogleMapUrl"]=igk_getr("clGoogleMapUrl");
    }
    /**
    * auto generate doc.
    */    public function View():BaseController{
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