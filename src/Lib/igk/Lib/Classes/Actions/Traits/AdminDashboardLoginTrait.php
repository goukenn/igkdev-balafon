<?php
// @author: C.A.D. BONDJE DOUE
// @file: AdminDashboardLoginTrait.php
// @date: 20230617 13:50:29
namespace IGK\Actions\Traits;

use IGK\System\Http\Request;
use IGKException;

///<summary></summary>
/**
* 
* @package IGK\Actions\Traits
*/
trait AdminDashboardLoginTrait{
    #region login - logout request
    public function logout_get(){
        $this->getController()->logout(); 
    }
    /**
     * login for admin dash board 
     * @param Request $request 
     * @return bool 
     * @throws IGKException 
     */
    public function login_post(Request $request){ 
        $notkey = $this->baseActionName ?? 'login';      
        $ctrl = $this->getController();
        $data = $errors = null;  
        if (!$this->getUser()){        
            if ($this->getValidator()->validate($_REQUEST, [
                'login'=>$request->getContentSecurity('LoginAccess'),          
                'password'=>$request->getContentSecurity('Password'),
            ]
            ,null,null, $data, $errors))
            {
                if ($ctrl->login($data->login, $data->password, false)){ 
                    $ctrl->setEnvParam('dashboard:login', 1);
                    return true;
                }else{
                    $this->notify_danger('login or password not valid. if you thing is an error please contact administrator.', $notkey);
                }
            } else if ($errors){
                $this->notify_danger($errors, $notkey); 
            }
        }
        return false;
    }
    #endregion 
}