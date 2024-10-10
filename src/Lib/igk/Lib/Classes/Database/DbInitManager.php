<?php
// @author: C.A.D. BONDJE DOUE
// @file: DbInitManager.php
// @date: 20221118 09:03:41
namespace IGK\Database;

use IGK\Controllers\BaseController;
use IGK\Models\Authorizations;
use IGK\Models\Groupauthorizations;
use IGK\Models\Groups;

///<summary></summary>
/**
* 
* @package IGK\Database
*/
class DbInitManager{
    public function init(BaseController $controller){
      
        // + | --------------------------------------------------------------------
        // + | init profiles
        // + |
        $this->initProfile($controller);
    }
    protected function initProfile(BaseController $controller){
        $tpro= null;
        $pro = $controller->configFile('profiles');
        if ($pro && file_exists($pro)){
            $tpro = include($pro);
        }
        if (!is_array($tpro))
            return;
        $roles = []; // mean groups
        $auths = []; // mean auths
        $fd_name = IGK_FD_NAME;
        $keyname = $controller ? igk_uri(get_class($controller)) : null;
        $v_auths = [];
        if ($keyname){
            $v_auths['clController'] = $keyname;
        } 
        foreach($tpro as $k=>$c){
            // init groups
            if (!isset($roles[$k])){                
                $roles[$k] = $this->_registerGroupAndAuth($k, $controller);  
            }
            if (is_string($c)){
                igk_wln_e("not ok: ".$pro);
            }
            // init auth 
            foreach($c as $m){
                if (!isset($auths[$m])){
                    if ($auth = igk_getv($roles, $m)){
                        $auth = $auth[1];
                    }
                    $v_auths[$fd_name] =  $controller::authName($m);                    
                    $auths[$m] = $auth ?? Authorizations::createIfNotExists($v_auths);  
                }
                if ($auths[$m] && $roles[$k][0]){
                    Groupauthorizations::createIfNotExists([
                        "clGrant"=>1, 
                        "clGroup_Id"=>$roles[$k][0]->clId,
                        "clAuth_Id"=>$auths[$m]->clId,
                    ]);
                } else{
                    igk_ilog("can't create pofile setting : ".get_class($controller));
                }
            } 
        }
    }
    /**
     * 
     * @param string $name 
     * @return (null|Groups|Authorizations)[] 
     */
    protected function _registerGroupAndAuth(string $name, ?BaseController $controller){
        $fd_name = IGK_FD_NAME;
        $top = [];
        if ($controller){
            $top['clController']= igk_uri(get_class($controller));
        }
        $top[$fd_name] = $name;
        $group = Groups::createIfNotExists($top); 
        $top[$fd_name] = $controller::authName($name);
        $auth = Authorizations::createIfNotExists($top);
        return [$group, $auth];
    }
}