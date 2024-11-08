<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKApp.php
// @date: 20220803 13:48:54
// @desc: 


///<summary> core application engine </summary>

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Manager\ApplicationControllerManager;
use IGK\Manager\IApplicationControllerManager;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\HookHandler; 
use IGK\System\IO\Path;
use function igk_resources_gets as __; 

require_once IGK_LIB_CLASSES_DIR.'/IGKEvents.php';
require_once IGK_LIB_CLASSES_DIR.'/IGKAppContext.php';
/**
 * 
 * @package IGK
 * @property IGKAppSetting $settings application setting - store in session if library 'session' is available
 * 
 */
class IGKApp extends IGKObject
{
    private static $sm_instance;
    private $m_application;
    private $m_appInfo;
    private $m_settings;
    private $m_controllerManager;
    /**
     * initialized
     * @var bool
     */
    private $m_initialized;
    private $_f;

    const DOC_NAME = "main_document";
    
    /**
     * get app configuration settings
     * @return mixed 
     */
    public static function GetConfig($key, $default=null){
        return IGKAppConfig::getInstance()->Data->get($key, $default);
    }
    /**
     * engine application
     * @return IGKApplicationBase 
     */
    public function getApplication(){
        return $this->m_application;
    }
    /**
     * return the application instance
     * @return mixed 
     */
    public static function getInstance()
    {
        return self::$sm_instance;
    }
    private function __construct(){
        
    }
    /** 
     * create controller manager
     * @return IApplicationControllerManager
     */
    protected function createControllerManager(): IApplicationControllerManager{
        //:: return IGKControllerManagerObject::getInstance();
        return new ApplicationControllerManager($this, $this->m_appInfo);
    }

    public function __toString()
    {
        return "igk framework - app[Version:" . IGK_VERSION . "]";
    }
    public function __set($n, $v)
    {
        igk_die(sprintf(__("app - setting property not allowed [%s]"), $n));
    }
    public function __call($name, $args){
        if (($app = $this->m_application) &&
            ($builder = $app->getBuilder())){
            $builder->$name(...$args);
            return $this;
        }
    }
    /**
     * retrieve application setting
     * @return mixed 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     * @throws Exception 
     */
    public function getSettings(){

        $app_key = IGK_APP_SESSION_KEY;
        $use_session = $this->getApplication()->lib("session");
        $v_bstart = null;        
        //$reset = 0;
        if ($this->m_settings && $use_session && isset($_SESSION) && (!isset($_SESSION[$app_key]) || ($_SESSION[$app_key] !==  $this->m_settings->getInfo())) ){
            $v_bstart = $this->m_settings;
            $this->m_settings = null; 
            igk_hook(IGKEvents::HOOK_APP_SETTING_RESET, [$this,session_id()]); 
        }
        if ($this->m_settings  === null){
            if ($use_session) {
                $v_setting_info = igk_create_session_instance($app_key, function()use(& $v_bstart){
                    $a =  ($v_bstart ? $v_bstart->getInfo() : null) ?? $this->createAppInfo();
                    $a->{IGK_SESSION_ID} = session_id();
                    $a->{IGK_APP_INFO_TYPE} = IGKAppInfoType::Session; 
                    return $a;
                });
                
            } else {
                $v_setting_info = $this->createAppInfo();
            }
            $this->m_settings = new IGKAppSetting($v_setting_info);             
            if (!$this->m_settings->appInfo->loaded){
                $this->m_settings->appInfo->loaded = 1;
            }
        }   
        return $this->m_settings;
    }
    ///<summary>get environment base controller</summary>
    /**
    * get environment base controller
    * @return BaseController|null base controller
    */
    public function getBaseCurrentCtrl(){
        return igk_environment()->basectrl;
    }
     ///<summary>change environment base controller</summary>
    ///<param name="v"></param>
    /**
    * change environment base controller
    * @param mixed $v
    */
    public function setBaseCurrentCtrl(?BaseController $v){    
		igk_environment()->basectrl =  $v; 
        return $this;
    }
    ///<summary>view mode setting - require session</summary>
    /**
    * view mode setting - require session
    */
    public function getViewMode(){

		if (!isset($this->getSettings()->{IGK_VIEW_MODE_FLAG}))
			return IGKViewMode::VISITOR;
        return $this->getSettings()->{IGK_VIEW_MODE_FLAG}; 
    }
     ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setViewMode($v){
        $m = $this->getViewMode();
        if($m === $v)
            return;
		if ($v ==  IGKViewMode::VISITOR)
			$v = null;
		$this->getSettings()->{IGK_VIEW_MODE_FLAG} = $v; 
        igk_hook(IGKEvents::HOOK_VIEW_MODE_CHANGED, [$this, $v]);
    }
   
     ///<summary>get api current page folder</summary>
    /**
    * get api current page folder
    */
    public function getCurrentPageFolder(){        
        $_is_phar=defined("IGK_PHAR_CONTEXT");
        if($_is_phar){
            $buri=igk_io_baseuri();
            $fulluri=igk_getv(explode("?", igk_io_fullrequesturi()), 0);
            if($buri != $fulluri){
                $ba=igk_dir(substr(igk_str_rm_last($fulluri, '/'), strlen($buri) + 1));
                if((strlen($ba) > 0) && is_dir(igk_io_basedir($ba))){
                    return $ba;
                }
            }
        }
        else{
            if(defined("IGK_CURRENT_PAGEFOLDER")){
                return IGK_CURRENT_PAGEFOLDER;
            }
            $cdir=IO::GetCurrentDir();
            $bdir=IO::GetBaseDir(); 
            if($cdir != $bdir)
                return IO::GetChildRelativePath($bdir, $cdir);
        }
        return IGK_HOME_PAGEFOLDER;
    }
    public function getCurrentPage(){
        return igk_getv(igk_getctrl(IGK_MENU_CTRL), 'CurrentPage', 'home');
    }
    /**
     * session data
     * @return IGKSession 
     * @throws IGKException 
     */
    public function getSession(){
        /**
         * @var object|null $sm_session session marker
         */
        static $sm_session=null;
        $init = false;
        if($sm_session === null){
            $init = true;
        }
        $appinfo = $this->getSettings()->appInfo;
        if (!$init && $appinfo && (!$sm_session || $sm_session->NoStore($appinfo ))){
            $init = 1;
        }
        if ($init){		
            /**
             * @var IGKAppInfoStorage $appinfo
             */      
			if (!$appinfo){                                    
                igk_die("can't create appinfo");                
            }  
            $tab = & $appinfo->getSession(); 
			$sm_session=new IGKSession($tab);
            igk_reg_hook(IGKEvents::HOOK_APP_SETTING_RESET, function()use(& $sm_session){
                $sm_session = null;
            });  
        }  
        return $sm_session;
    }
    /**
     * get controller manager instance 
     * @return \IGK\Controllers\IControllerManagerObject controller manager
     * @throws IGKException 
     */
    public function getControllerManager(){
        if (is_null($this->m_controllerManager) && !($this->m_controllerManager = $this->createControllerManager())){
            igk_die(__("failed to create app's controller manager"));
        }
        return $this->m_controllerManager; 
    }
    ///<summary>application configuration data</summary>
    /**
    * short cut to get application configuration data
    * @return IGK\System\Configuration\ConfigData
    */
    public function getConfigs(){
        return IGKAppConfig::getInstance()->Data;
    }
    ///<summary>get the global document</summary>
    /**
    *  get the global document
    * @return IGKHtmlDoc core document
    */
    public function getDoc(){
        static $v_doc=null;
        if(!self::IsInit()){            
            igk_die("can't get core document - application not initialized");
            return null;
        }
        if (is_null($v_doc)){ 	
            if (!igk_environment()->get(__METHOD__))
            {
                igk_environment()->set(__METHOD__, 1);
                $v_doc = IGKHtmlDoc::CreateCoreDocument(0);        
                $v_doc->setParam(IGK_DOC_ID_PARAM, self::DOC_NAME);
                igk_environment()->set(__METHOD__, null);
            } 
        }
        return $v_doc;        
    }

    public static function IsInit(){
        return (self::$sm_instance !==null) && self::$sm_instance->m_initialized;
    }
    /**
     * balafon engine
     * @return void 
     */
    public static function StartEngine(IGKApplicationBase $app, $render = 1)
    {              
        // | --------------------------------------------------------------
        // | init environment
        // |
        if ( self::$sm_instance !=null){
            // igk_trace();
            igk_die("App already started...");
        }
        igk_environment()->write_debug("StartEngine: ". igk_sys_request_time());
        self::$sm_instance = new self();
        self::$sm_instance->m_application = $app;
        $_hookArgs = ["app"=>self::$sm_instance, "render"=>$render];
        igk_environment()->set(IGK_ENV_APP_CONTEXT, IGKAppContext::starting); 
        if (!$app->getNoEnvironment()){
            IGKAppSystem::InitEnv(Path::getInstance()->getBaseDir(), self::$sm_instance);        
        }   
        self::_InitHookLogic($_hookArgs);   
    }
    private static function _InitHookLogic($_hookArgs){
        IGKEvents::hook(IGKEvents::HOOK_BEFORE_INIT_APP, $_hookArgs);  
        // + |--------------------------------------------------------------
        // + | HOOK application initialize 
        // + | 
  
        // \IGK\System\Diagnostics\Benchmark::Activate(true, ["dieOnError"=>true]);
        
        \IGK\System\Diagnostics\Benchmark::mark("hook_init_app");       
        // TODO : REMOVE HOOK_INIT_APP COAST        
        IGKEvents::hook(IGKEvents::HOOK_INIT_APP, $_hookArgs);  
        \IGK\System\Diagnostics\Benchmark::expect("hook_init_app", 0.0015); 
        self::$sm_instance->m_initialized = true;
        IGKEvents::hook(IGKEvents::HOOK_AFTER_INIT_APP, $_hookArgs);
    }

    /**
     * Run Api Engine context
     * @param IGKApplicationBase $app 
     * @return static 
     */
    public static function RunApiEngine(IGKApplicationBase $app): IGKApp {    
        if ( self::$sm_instance !=null){
            igk_die("App already started ... ");
        }
        $i = new self;
        $i->m_application = $app;
        self::$sm_instance = $i; 
        igk_environment()->set(IGK_ENV_APP_CONTEXT, IGKAppContext::starting);
        self::$sm_instance->m_initialized = true;
        return self::$sm_instance;
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
            IGK_APP_REQUEST_URI => igk_getv($_SERVER, "REQUEST_URI"),
            IGK_APP_CURRENT_DOC_INDEX_ID => -1,
            IGK_APP_INFO_TYPE => IGKAppInfoType::Local,
            "appInfo" => (new IGKAppInfoStorage())->getData() 
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
    /**
     * get service 
     * @param string $serviceName 
     * @return ?IApplicationService|mixed service to return  
     */
    public function getService(string $serviceName){
        return IGKServices::Get($serviceName);
    }

    //-------------------------------------------------------------------------------------
    // |+ self hosted application context

    private $m_context;

    /**
     * get the current definition context
     * @return ?string
     */
    public function getContext(): ?string{
        return $this->m_context;
    }
    /**
     * init application in self hosted
     * @return IGKApp 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Init(){
        if (!is_null(self::$sm_instance)){
            igk_die('application already initialized : '.self::$sm_instance->m_context );
        }
        self::$sm_instance = new static;
        self::$sm_instance->m_context = __METHOD__;
        igk_environment()->set(IGK_ENV_APP_CONTEXT, IGKAppContext::starting);        
        $_hookArgs = ["app"=>self::$sm_instance];
        $callback = ['invoke'];
        $g = new HookHandler([self::class, 'BootLogicCallback'], [
            "hookArgs"=>$_hookArgs,
            "callback"=> & $callback
        ]);
        array_unshift($callback, $g);       
        igk_reg_hook(IGKEvents::HOOK_APP_BOOT, $callback);
        return self::$sm_instance;
    }
    /**
     * hook logic callback
     * @param mixed $e 
     * @return void 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function BootLogicCallback($e){       
        $app = $e->args[0];
        $_hookArgs = $e->args[1]['hookArgs'];
        $_callback = $e->args[1]['callback'];
        igk_unreg_hook(IGKEvents::HOOK_APP_BOOT, $_callback);
        self::$sm_instance->m_application = $app;
        self::$sm_instance->m_initialized = __METHOD__;
        // boot environment - environment can handle request logic 
        IGKAppSystem::LoadEnvironment(self::$sm_instance); 
        // init hook logics
        self::_InitHookLogic($_hookArgs);        
    }
}

 