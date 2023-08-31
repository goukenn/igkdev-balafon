<?php
// @author: C.A.D. BONDJE DOUE
// @filename: subdomain.php
// @date: 20220803 13:48:55
// @desc:  subdomain library loading

 
namespace IGK\System\Library;
use function igk_resources_gets as __;
use Exception;
use IGK\Helper\SysUtils;
use IGK\System\Http\ConfigurationPageHandler;
use IGKEvents;
use IGKException;
use IGKSubDomainManager;
use IGKSystemUriActionPatternInfo;
use IGKValidator;

/**
 * subdomain management library
 * @package IGK\System\Library
 */
class subdomain{
    var $subdomain;
    var $subdomainInfo;
    var $boot_args; 
    public function init():bool{
        if (defined('IGK_NO_SUBDOMAIN')){
            return false;
        }
        require_once IGK_LIB_CLASSES_DIR."/IGKSubDomainManager.php";

        if (empty(IGKSubDomainManager::GetSubDomain())){
            return false;
        }
        if (!defined('IGK_CONFIG_PAGE') && !igk_is_cmd() && !IGKValidator::IsIPAddress(igk_server()->SERVER_NAME)){

            // igk_reg_hook(IGKEvents::HOOK_APP_BOOT, [$this, 'bootapp']);    
            igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function($e){
                $this->boot_args = $e;
                $this->bootapp();
            }, IGKEvents::P_SUBDOMAIN_PRIORITY);    
            return true;
        }
        return false;
    }
    public function bootapp(){
        IGKSubDomainManager::Init();
        $c = $this->boot_args;
        // igk_reg_hook(IGKEvents::HOOK_BEFORE_INIT_APP, function($c){            
            $app = $c->args["app"]->getApplication();
            if (!$app->lib("subdomain") ||
                ($app->getLibrary()->subdomain !== $this)
            )
            {
                return;
            } 
            if (igk_app()->getCurrentPageFolder() == IGK_CONFIG_PAGEFOLDER){
                return;
            }  
            // register after init app 
            igk_reg_hook(IGKEvents::HOOK_AFTER_INIT_APP , function($e){
                $file = $e->args['app']
                ->getApplication()->getEntryfile() ?? igk_getv($_SERVER, 'SCRIPT_FILENAME');   
                $this->__checkSubDomain($file);
            });
        //}, 100);
    }

    ///<summary></summary>
    /**
    * check for subdomain
    */
    private function __checkSubDomain(?string $file){
       
        $uri=igk_io_fullrequesturi();
        if(igk_io_handle_system_command($uri)){
            igk_exit();
        }
        $row=null;
        $subdomain_ctrl = IGKSubDomainManager::getInstance()->checkDomain(null, $row);       
        if($subdomain_ctrl !== false){ 
            SysUtils::SecurePort();
            $v_ruri=igk_io_base_request_uri(); 
            $this->subdomain = $subdomain_ctrl ; 
            $this->subdomainInfo = $row;
            $app= igk_app();
            // + | reset configuration page
            $app->settings->appInfo->store("config", null);
            // igk_ilog([__FILE__.":".__LINE__, "reset config"]);
            $tab=explode('?', $v_ruri);
            $uri=igk_getv($tab, 0);
            $params=igk_getv($tab, 1);
            $entry="";
            if($row){
                $e = $row->clView ?? null;
                if($e && !empty($e=trim($e)))
                    $entry="/".$e;
            }

            require_once IGK_LIB_DIR . "/igk_request_handle.php";       
            igk_sys_handle_ctrl_request_uri($uri, true); 
            $path_info = $uri;
            if (!igk_environment()->noWebConfiguration()) {
                (new  ConfigurationPageHandler(function (bool $display) {
                    // $this->runEngine($display);                    
                }, $file))->handle_route($path_info, null);
            } 
            $page="{$entry}".$uri;
            $actionctrl=igk_getctrl(IGK_SYSACTION_CTRL);
            $k=IGK_REG_ACTION_METH;
            $pattern=igk_sys_ac_getpattern($k);
            $e=new IGKSystemUriActionPatternInfo(array(
                "action"=>$k,
                "value"=>$subdomain_ctrl->getRegInvokeUri(),
                "pattern"=>$pattern,
                "uri"=>$page,
                "keys"=>igk_str_get_pattern_keys($k),
                "ctrl"=>$subdomain_ctrl , 
                "requestparams"=>$params,
                "context"=>"subdomain"
            ));
            if($actionctrl && ($subdomain_ctrl !== $actionctrl)){ 
                $app->Session->RedirectionContext=1; 
                if(!$subdomain_ctrl->NoGlobalAction && ($ce=$actionctrl->matche_global($page))){
                    try {
                        $ce->ctrl=null;
                        $actionctrl->invokeUriPattern($ce); 
                    }
                    catch(Exception $e){
                        igk_show_exception($e);
                        igk_exit();
                    }
                    return;
                }
                else {
                    $actionctrl->invokeCtrlUriPattern($subdomain_ctrl, $e);
                }
            } 
        }
        else{
            $s=igk_io_subdomain_uri_name();
            if(!empty($s) && !strstr(igk_configs()->website_domain, $s)){
                if(igk_server()->REQUEST_PATH == '/'){
                    $msg=__("Subdomain not accessible : {0}", $s);
                    if($def_ctrl=igk_get_defaultwebpagectrl()){
                        $def_ctrl->handleException(new IGKException($msg, 500), "subdomain error"); //.$def_ctrl->getTitle());
                    }
                    else{
                        igk_set_header(500);
                        igk_wln_e('[igk]', $msg);
                    }
                }
            }
        } 
        // + | --------------------------------------------------------------------
        // + | disable subdomain info
        // + |
        $this->subdomain = null;
        $this->subdomainInfo = null; 
    }
}