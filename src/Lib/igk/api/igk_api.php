<?php
// @file: igk_api.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

use IGK\Controllers\ApplicationController;
use IGK\Database\DbSchemas;
use IGK\Helper\IO;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\Html\HtmlReader;
use IGK\System\Html\HtmlRenderer;

use function igk_resources_gets as __;
define("IGK_API_CTRL", "API");
define("IGK_API_VERSION", "2.1.1.0921");
define("IGK_API_URI", "^/api/v2");
define("IGK_API_LIB", dirname(__FILE__));
define("IGK_API_MYSQLPINC", realpath(IGK_API_LIB."/.mysql.pinc"));
require_once(IGK_API_LIB."/.igk.api.func.pinc");
// require_once(IGK_LIB_CLASSES_DIR."/ApplicationController.php");

///<summary></summary>
/**
* 
*/
function igk_api_free_session(){
    if(!igk_server_request_onlocal_server()){
        if(igk_getr("clClearS")){
            igk_app_destroy();
            session_destroy();
        }
    }
}

///<summary> evaluate entries</summary>
/**
 *  evaluate entries
 */
function igk_api_sync_def_evaluate_entries($entries, $table_n, $mysql, $db, $tables){
    $n=$entries->addNode("Rows")->setAttribute("For", $table_n);
    $list=$tables->list[$table_n];
    $links=$list["Links"];
    $auto=$list["auto"];
    $fc_update=function($tn) use (& $auto, $table_n, $db){
        if($auto){
            $tn[$auto]=null;
        }
        $v=$tn->getParam("dbRow");
        $tn->setAttr("igk:id", $db->getSyncIdentificationId($table_n, $v));
    };
    if(igk_count($links) > 0){
        $rows=$mysql->select($table_n)->Rows;
        foreach($rows as $v){
            $tn=$n->addNode("Row")->setAttributes($v);
            $tn->setParam("dbRow", $v);
            $continu=false;
            foreach($links as $vlnk){
                foreach($vlnk as $g){
                    $cn=$g["Column"];
                    $ftn=$g["Table"];
                    $clvalue=$tn[$cn];
                    $bck=igk_getv($tables->list, $ftn);
                    $v_mk=$ftn."/".$clvalue;
                    if($bck && (igk_count($bck["Links"]) > 0)){
                        $v_data=($v_data=igk_getv($tables->values, $v_mk)) ? $v_data: $db->getSyncDataValueDisplay($ftn, $clvalue, $tables);
                        $tn[$cn]=$v_data;
                        $tables->values[$v_mk]=$v_data;
                    }
                    else{
                        $v_data=($v_data=igk_getv($tables->values, $v_mk)) ? $v_data: $db->getSyncDataValueDisplay($ftn, $clvalue);
                        $tn[$cn]=$v_data;
                        $tables->values[$v_mk]=$v_data;
                    }
                }
                if($continu)
                    break;
            }
            $fc_update($tn);
        }
    }
    else{
        foreach($mysql->select($table_n)->Rows as $v){
            $tn=$n->addNode("Row")->setAttributes($v);
            $tn->setParam("dbRow", $v);
            $fc_update($tn);
        }
    }
}


