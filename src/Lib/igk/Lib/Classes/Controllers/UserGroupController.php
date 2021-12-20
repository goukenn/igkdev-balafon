<?php
// @file: IGKUserGroupController.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Controllers;

use IGKEvents;

final class UserGroupController extends NonVisibleControllerBase{
    ///<summary></summary>
    public function getDataTableInfo(){
        return null;
    }
    ///<summary></summary>
    public function getDataTableName(){
        return igk_db_get_table_name(IGK_TB_USERGROUPS);
    }
    ///<summary></summary>
    protected function registerHook(){
        $tb=$this->getDataTableName();
        igk_reg_hook(IGKEvents::HOOK_DB_DATA_ENTRY, function($hook) use ($tb){
            igk_wln_e(__FILE__.":".__LINE__,  "init data entries");
            if($hook->args[1] == $tb){
                $db=$hook->args[0];
                $db->insert($tb, array(IGK_FD_USER_ID=>1, IGK_FD_GROUP_ID=>2));
            }
        });
    }
}
