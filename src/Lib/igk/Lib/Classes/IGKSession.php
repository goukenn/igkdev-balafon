<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKSession.php
// @date: 20220803 13:48:54
// @desc: 

use IGK\IParamHostService;
use IGK\Resources\R;
use function igk_resources_gets as __;
/**
* represent handle session service
* @property $services stored services
*/
final class IGKSession extends IGKObject implements IParamHostService {

    /**
    * Constant: base sess param.
    * @var mixed
    */
    const BASE_SESS_PARAM=0x020;

    /**
    * Constant: igk domainbasedir sess param.
    * @var mixed
    */
    const IGK_DOMAINBASEDIR_SESS_PARAM=(self::BASE_SESS_PARAM + 0x005);

    /**
    * Constant: igk instances sess param.
    * @var mixed
    */
    const IGK_INSTANCES_SESS_PARAM=(self::BASE_SESS_PARAM + 0x006);

    /**
    * Constant: igk redirection sess param.
    * @var mixed
    */
    const IGK_REDIRECTION_SESS_PARAM=(self::BASE_SESS_PARAM + 0x004);

    /**
    * Constant: sess controllerparam key.
    * @var mixed
    */
    const SESS_CONTROLLERPARAM_KEY=(self::BASE_SESS_PARAM + 0x00C);

    /**
    * Constant: sess cref key.
    * @var mixed
    */
    const SESS_CREF_KEY=(self::BASE_SESS_PARAM + 0x001);

    /**
    * Constant: sess domain.
    * @var mixed
    */
    const SESS_DOMAIN=(self::BASE_SESS_PARAM + 0x00A);

    /**
    * Constant: sess domain basefile.
    * @var mixed
    */
    const SESS_DOMAIN_BASEFILE=(self::BASE_SESS_PARAM + 0x009);

    /**
    * Constant: sess global theme.
    * @var mixed
    */
    const SESS_GLOBAL_THEME=(self::BASE_SESS_PARAM + 0x007);

    /**
    * Constant: sess lang key.
    * @var mixed
    */
    const SESS_LANG_KEY=(self::BASE_SESS_PARAM + 0x00B);

    /**
    * Constant: sess pagefolder key.
    * @var mixed
    */
    const SESS_PAGEFOLDER_KEY=(self::BASE_SESS_PARAM + 0x003);

    /**
    * Constant: sess session events.
    * @var mixed
    */
    const SESS_SESSION_EVENTS=(self::BASE_SESS_PARAM + 0x008);

    /**
    * Constant: sess user key.
    * @var mixed
    */
    const SESS_USER_KEY=(self::BASE_SESS_PARAM + 0x0002);

    /**
    * Constant: sess service.
    * @var mixed
    */
    const SESS_SERVICE=(self::BASE_SESS_PARAM + 0x000F);

    /**
    * Constant: sysdb ctrl.
    * @var mixed
    */
    const SYSDB_CTRL=IGK_KEY_SYSDB_CTRL;

    /**
    * Constant: globalvars.
    * @var mixed
    */
    const GLOBALVARS = (self::BASE_SESS_PARAM + 0x0100);

    /**
    * Property: instances.
    * @var mixed
    */
    private $m_instances;

    /**
    * Property: session params.
    * @var mixed
    */
    private $m_sessionParams;

    /**
    * auto generate doc.
    * @param mixed * $params
    */

    public function __construct(& $params){
        $this->m_sessionParams=& $params;   
    }

    /**
    * auto generate doc.
    * @return mixed|array return configured routes
    */

    public function & getRoutes(){
		$s = igk_app()->getSettings();
		if (($r = $s->{IGK_SESS_ROUTES}) === null){
			$r = [];
			$s->{IGK_SESS_ROUTES} = $r;
		}
		return $r;
	}
    /**
     * auto inject or update the value 
     * @param string $name 
     * @param null|array $args 
     * @return mixed|void 
     * @throws Exception 
     */

    public function __call(string $name, ?array $args){
        if ($args && (($fc=igk_getv($args, 0)) instanceof Closure)){
            return $this->updateProperty($name, $fc); 
        }
    }
    /**
     * update property 
     * @param string $name 
     * @param callable $callable 
     * @return mixed 
     */

    public function updateProperty(string $name, callable $callable){
        $c = $this->$name;
        $c = $this->$name = $callable($c) ?? $c;
        return $c;
    }

    /**
    * auto generate doc.
    * @param mixed $key
    */

    public function __get($key){
		$g = null;
        if(method_exists($this, "get".$key)){
            $g= call_user_func(array($this, "get".$key), null);
        }
        else if(isset($this->m_sessionParams[$key])){
            return $this->m_sessionParams[$key];
        }
        return $g;
    }

    /**
    * check if isset innaccessible property
    * @param mixed $key
    */
    public function __isset($key){
        return isset($this->m_sessionParams[$key]);
    }

    /**
    * Returns Reference.
    * @param mixed $name
    */
    public function & getReference($name){
        $tab = null;
        if (isset($this->m_sessionParams[$name])){
            $tab = & $this->m_sessionParams[$name];
        }
        return $tab;
    }
    /**
     * get session id
     * @return string|false 
     */

    public function id(){
        return session_id();
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function __set($key, $value){
        if(!$this->_setIn($key, $value)){
            if($value == null)
                unset($this->m_sessionParams[$key]);
            else{
                $this->m_sessionParams[$key]=$value;
            }
        }
    }
    ///igk_wln("set : ".count($this->m_sessionParams), $key);

    /**
    * auto generate doc.
    */

    public function __toString(){
        //+ ASS: Appliation session storage
        return get_class($this)."[::ASS]";
    }
    /**
    * 
    */
    private function _onUserChanged(){
        igk_invoke_session_event(__CLASS__."::UserChanged", array($this, null));
    }

    /**
    * auto generate doc.
    * @param mixed $method
    */

    public function addInitializeSessionEvent($obj, $method){}

    /**
    * auto generate doc.
    * @param mixed $method
    */

    public function addUserChangedEvent($obj, $method){}

    /**
    * auto generate doc.
    * @param mixed $key
    */

    public function Clear($key){
        if(isset($this->m_sessionParams[$key]))
            unset($this->m_sessionParams[$key]);
    }
    /**
    * register a custom uri compoent to reg uris
    */

    public function component($uri, $setting){
        $c=$this->regUris;
        if(!$c){
            $c=array();
        }
        $c[$uri]=$setting;
        $this->regUris=$c;
    }
    /**
    * create instance item for session
    */

    public function createInstance($class, $callback=null){
        if($this->m_instances == null){
            $this->m_instances=array();
        }
        if(isset($this->m_instances[$class])){
            if(get_class(($cl=$this->m_instances[$class])) != $class){
                die(__("Class instance changed:{0}", $class));
            }
            return $cl;
        }
        if($callback != null){
            $cl=$callback();
        }
        else{
            $cl=new $class();
        }
        if($cl == null){
            die(__("Failed to create instance fo ".$class));
        }
        $this->m_instances[$class]=$cl;
        return $cl;
    }

    /**
    * auto generate doc.
    */

    public function generateCref(){
        $cref = igk_create_cref();
		igk_app()->getSettings()->{IGK_FORM_CREF} = $cref; 
        return $cref;
    }

    /**
    * auto generate doc.
    */

    public function getApp(){
        return igk_app();
    }
    /**
    * get controller params 
    */

    public function & getControllerParams(){
        $p = & igk_app()->settings->appInfo->ctrlParams;
        return $p;
    }

    /**
    * Returns Form.
    */
    public function getForm(){
		if (!igk_app()->settings->form){
			igk_app()->settings->form = (object)[];
		}
		return igk_app()->settings->form;
	}

    /**
    * auto generate doc.
    */

    public function getCRef(){   
        $cref= igk_app()->settings->{IGK_FORM_CREF} ?? (function(){    
            return $this->generateCref();
        })(); 
        return $cref;
    }
    /**
    * get session data
    * @return mixed|array params
    */

    public function & getData(){
        return $this->m_sessionParams;
    }

    /**
    * No store.
    * @param mixed $data
    */
    public function NoStore($data){
        return $this->m_sessionParams === $data;
    }

    /**
    * auto generate doc.
    */

    public function getDomain(){
        return $this->getParam(self::SESS_DOMAIN);
    }

    /**
    * auto generate doc.
    */

    public function getDomainBaseDir(){
        return $this->getParam(self::IGK_DOMAINBASEDIR_SESS_PARAM);
    }

    /**
    * auto generate doc.
    */

    public function getdomainBaseFile(){
        $c= $this->getParam(self::SESS_DOMAIN_BASEFILE);
        if  ($c){
            $c = igk_dir(str_replace("%basepath%", igk_io_basedir(), $c));            
        }
        return $c;
    }

    /**
    * auto generate doc.
    */

    public function getEvents(){
        return $this->getParam(self::SESS_SESSION_EVENTS);
    }

    /**
    * auto generate doc.
    */

    public function getLang(){
		$g = igk_app()->getSettings()->{self::SESS_LANG_KEY};
        return $g ??  R::GetDefaultLang();
    }

    /**
    * auto generate doc.
    */

    public function getPageFolder(){
        if($p=$this->getParam(self::SESS_PAGEFOLDER_KEY)){
            return $p;
        }
        return IGK_HOME_PAGEFOLDER;
    }

    /**
    * auto generate doc.
    * @param mixed $default the default value is null
    */

    public function getParam($key, $default=null){
        if(isset($this->m_sessionParams[$key])){
            return $this->m_sessionParams[$key];
        }
        if(igk_is_callable($default)){
            $o=$default();
            if($o){
                $this->m_sessionParams[$key]=$o;
            }
            return $o;
        }
        return $default;
    }

    /**
    * auto generate doc.
    */

    public function getParamKeys(){
        return array_keys($this->m_sessionParams);
    }

    /**
    * auto generate doc.
    */

    public function getRedirectTask(){
        $i=null;
        $name=null;
        if(func_num_args() > 0)
            $name=func_get_arg(0);
        $g=$this->prepareRedirectTask();
        if($g){
            $i=(object)$g;
            if($name)
                return igk_getv($i, $name);
        }
        return $i;
    }

    /**
    * auto generate doc.
    * @param mixed $classname
    * @return mixed|array controller parameters
    */

    public function & getRegisteredControllerParams($classname){
        $g=null;
		$t = & $this->getControllerParams();
		if (isset($t[$classname]))
			$g = & $t[$classname];       
        return $g;
    }

    /**
    * auto generate doc.
    */

    public function getUser(){
        return $this->getParam(self::SESS_USER_KEY);
    }

    /**
    * auto generate doc.
    */

    public function getUserChangedEvent(){
        return $this->m_UserChangedEvent;
    }

    /**
    * auto generate doc.
    * @param mixed $app
    */

    public function initalize($app){
        if($app == $this->m_igk){
            $this->m_initializeSessionEvent->Call($this, null);
        }
    }
    /**
    * 
    */
    private function prepareRedirectTask(){
        $g=igk_get_env("sys://session/redirecttask");
        if($g)
            return $g;
        $g=$this->{"REDIREC_TASK"}
         ?? array();
        igk_get_env("sys://session/redirecttask", $g);
        return $g;
    }

    /**
    * auto generate doc.
    * @param mixed $method
    */

    public function removeInitializeSessionEvent($obj, $method){}
    /**
    * reset param 
    */

    public function resetParam(){
        $this->m_sessionParams=array();
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setDomain($v){
        $this->setParam(self::SESS_DOMAIN, $v);
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setDomainBaseDir($v){
        $this->setParam(self::IGK_DOMAINBASEDIR_SESS_PARAM, $v);
    }

    /**
    * auto generate doc.
    * @param mixed $v
    */

    public function setdomainBaseFile($v){
        $bpth = igk_io_basepath($v);
        if  ($bpth){
            $v = "%basepath%/".$bpth;
        }
        return $this->setParam(self::SESS_DOMAIN_BASEFILE, $v);
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setEvents($value){
        $this->setParam(self::SESS_SESSION_EVENTS, $value);
    }

    /**
    * auto generate doc.
    * @param mixed $lang
    */

    public function setLang($lang){
        $c=R::GetDefaultLang();
		$l = $this->getLang();
		if ($l!=$lang){
        	igk_app()->settings->{self::SESS_LANG_KEY} = $lang;
        }
        return $this;
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setPageFolder($value){
        $this->setParam(self::SESS_PAGEFOLDER_KEY, $value);
    }
    /**
    * set session param
    */

    public function setParam($key, $value){
        if(empty($key))
            return;
        if(isset($this->m_sessionParams[$key])){
            if($value == null)
                unset($this->m_sessionParams[$key]);
            else
                $this->m_sessionParams[$key]=$value;
        }
        else{
            if($value != null){
                $this->m_sessionParams[$key]=$value;
            }
        }
    }

    /**
    * auto generate doc.
    * @param mixed $value
    */

    public function setRedirectTask($name, $value){
        $g=$this->{"REDIREC_TASK"}
         ?? array();
        if($value === null){
            unset($g[$name]);
        }
        else
            $g[$name]=$value;
        $this->{"REDIREC_TASK"}=$g;
    }
    /**
     * just logout 
     * @return void 
     */

    public function logout(){
        $this->setParam(self::SESS_USER_KEY, null);
    }
    /**
    * set user 
    * @param mixed $user
    * @param mixed $context require context only USER_CTRL can call this method
    */

    public function setUser($user, $context){  
        $u=$this->getUser();
        if(($context !== null) && ($context == igk_getctrl(IGK_USER_CTRL))){
            if($u !== $user){
                if ($user && (get_class($user) !== IGKUserInfo::class)){
                    $user = igk_sys_create_user($user);
                }
                $this->setParam(self::SESS_USER_KEY, $user);
                $this->_onUserChanged();
            }
        }
        else{
            igk_die("Operation not  allowed ".__FUNCTION__);
        }
    }
    ///
    /**
    * raise the session UpdateEVent
    */

    public function update(){    
        $this->__set(IGKSession::IGK_REDIRECTION_SESS_PARAM, null);
        if($this->m_updateSessionEvent != null)
            $this->m_updateSessionEvent->Call($this, null);
    }

    /**
    * Returns Services.
    */
    public function getServices(){
        return $this->getParam(self::SESS_SERVICE);
    }

    /**
    * Sets Services.
    * @param null|array $service
    */
    public function setServices(?array $service=null){
        if ($service ==null){
            unset($this->m_sessionParams[self::SESS_SERVICE]  );
        }else {
            $this->m_sessionParams[self::SESS_SERVICE] =$service; 
        }
    } 
    /**
     * update store value
     * @param mixed $key 
     * @param mixed|callable $value 
     * @return mixed 
     */

    public function updateValue($key, $value){
        $rt = $this->$key;
        if (is_callable($value)){
            $rt = $value($rt);
        } else {
            if (!$rt){
                $rt = $value;
            }
        }
        $this->$key = $rt;
        return $rt;
    }
    /**
     * get session id
     * @return string|false|int 
     */

    public function session_id(){
        if (igk_app()->getApplication()->lib("session")){
            return session_id();
        }
        return -1;
    }
    /**
     * retrieve value of key 
     * @param string $key 
     * @param mixed $default 
     * @return mixed 
     */

    public function get(string $key, $default){
        if (isset($this->$key)){
            return $this->$key;
        }
        return $default;
    }
    /**
     * get value and reset 
     */

    public function getr(string $key, $default){
        $m = $this->get($key, $default);
        $this->{$key} = null;
        return $m;
    }
}