<?php

///<summary> core application engine </summary>

use IGK\System\Html\HtmlRenderer;
use IGK\System\IO\Path;

class IGKApp extends IGKObject
{
    private static $sm_instance;
    private $m_application;
    private $m_appInfo;
    private $m_settings;
    private $m_controllerManager;
    private $_f;

    const DOC_NAME = "main_document";
    
    /**
     * return the application instance
     * @return mixed 
     */
    public static function getInstance()
    {
        return self::$sm_instance;
    }
    private function __construct()
    {
    }

    public function __toString()
    {
        return "igk framework[Version:" . IGK_VERSION . "]";
    }
    public function __set($n, $v)
    {
        igk_die("not allowed " . $n);
    }
    public function getSettings(){
        return $this->m_settings;
    }
    public function getSession(){
        static $sm_session=null;
        if($sm_session === null){
			$tab = null;
            $appinfo = $this->getSettings()->appInfo;
			if (!$appinfo){                                    
                    igk_die("can't create appinfo");                
            }            
            if(isset($appinfo->session)){
                $tab= & $appinfo->session;
            }
            else{
                //+ get array reference
                $tab=array();
				$appinfo->session = & $tab;
            }
			$sm_session=new IGKSession($this, $tab);
            $this->_f[IGK_SESS_FLAG] = & $sm_session->getData();
        }
        return $sm_session;
    }
    public function getControllerManager(){
        return IGKControllerManagerObject::getInstance();
    }
    ///<summary>application configuration data</summary>
    /**
    * short cut to get application configuration data
    */
    public function getConfigs(){
        return IGKAppConfig::getInstance()->Data;
    }
      ///<summary> get the global document</summary>
    /**
    *  get the global document
    * @return IGKHtmlDoc core document
    */
    public function getDoc(){
        static $v_doc=null;
        if(!self::IsInit()){
            igk_session_destroy();
            igk_die("can't get core document - application not initialized");
            return null;
        }
        if ($v_doc === null){
            // igk_wln("init document ......".igk_sys_request_time());	
            if (!igk_environment()->get(__METHOD__))
            {
                igk_environment()->set(__METHOD__, 1);
                igk_start_time(__METHOD__); 
                $v_doc = IGKHtmlDoc::CreateCoreDocument(0);        
                $v_doc->setParam(IGK_DOC_ID_PARAM, self::DOC_NAME);
                igk_environment()->set(__METHOD__, null);
            } 
        }
        return $v_doc;        
    }

    public static function IsInit(){
        return self::$sm_instance !==null;
    }
    /**
     * balafon engine
     * @return void 
     */
    public static function StartEngine(IGKApplicationBase $app, $render = 1)
    {
        //
        // init environment
        //
        IGKAppSystem::InitEnv(Path::getInstance()->getBaseDir());

        // igk_wln_e(get_included_files(), "duddration ". igk_sys_request_time());

        self::$sm_instance = new self();
        self::$sm_instance->m_application = $app;
        igk_environment()->set(IGK_ENV_APP_CONTEXT, IGKAppContext::starting);

        // + initialize library
        $app->libary("session");
        $app->libary("mysql");
        $app->libary("zip");
        $app->libary("gd");

        // + | init session data
        $v_setting_info = null;
        if ($app->lib("session")) {
            $v_setting_info = igk_create_session_instance("igk", function(){
                return call_user_func_array([self::$sm_instance, 'createAppInfo'], []);
            });
        } else {
            $v_setting_info = self::$sm_instance->createAppInfo();
        }
        self::$sm_instance->m_settings = new IGKAppSetting($v_setting_info);      
     
        // + |
        // + | INIT CONTROLLER LIST
        // + | HOOK application initilize
        // + |
        igk_hook(IGKEvents::HOOK_INIT_APP, [self::$sm_instance]);
        
        if ($render) {
            // render document
            HtmlRenderer::RenderDocument(self::$sm_instance->getDoc());
        }
    }

    /**
     * create session storage application information
     * @return object 
     * @throws IGKException 
     */
    protected function createAppInfo()
    {
        return  (object)[
            IGK_CREATE_AT => date(IGK_DATETIME_FORMAT),
            IGK_VERSION_ID => IGK_VERSION,
            IGK_CLIENT_IP => igk_getv($_SERVER, "REMOTE_ADDR"),
            IGK_CLIENT_AGENT => igk_getv($_SERVER, "HTTP_USER_AGENT"),
            IGK_SESSION_ID => "",
            IGK_CURRENT_DOC_INDEX_ID => -1,
            "appInfo" => (object)[
                "controllers" => [],
                "documents" => [],
                "components" => igk_prepare_components_storage()
            ]
        ];
    }

    ///<summary>destroy the application</summary>
    /**
    * destroy the application
    */
    public static function Destroy(){
        if(self::$sm_instance !== null){
            igk_hook("sys://events/destroyapp", [self::$sm_instance, __FUNCTION__]);
            self::$sm_instance=null;
            return 1;
        }
        return 0;
    }
}
