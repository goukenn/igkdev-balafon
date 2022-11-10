<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKSession.php
// @date: 20220803 13:48:54
// @desc: 


///<summary>represent handle session service</summary>
///<remark> only create when required . from session param</remark>

use IGK\Resources\R;
use function igk_resources_gets as __;


/**
* represent handle session service
* @property $services stored services
*/
final class IGKSession extends IGKObject implements IIGKParamHostService {
    const BASE_SESS_PARAM=0x020;
    const IGK_DOMAINBASEDIR_SESS_PARAM=(self::BASE_SESS_PARAM + 0x005);
    const IGK_INSTANCES_SESS_PARAM=(self::BASE_SESS_PARAM + 0x006);
    const IGK_REDIRECTION_SESS_PARAM=(self::BASE_SESS_PARAM + 0x004);
    const SESS_CONTROLLERPARAM_KEY=(self::BASE_SESS_PARAM + 0x00C);
    const SESS_CREF_KEY=(self::BASE_SESS_PARAM + 0x001);
    const SESS_DOMAIN=(self::BASE_SESS_PARAM + 0x00A);
    const SESS_DOMAIN_BASEFILE=(self::BASE_SESS_PARAM + 0x009);
    const SESS_GLOBAL_THEME=(self::BASE_SESS_PARAM + 0x007);
    const SESS_LANG_KEY=(self::BASE_SESS_PARAM + 0x00B);
    const SESS_PAGEFOLDER_KEY=(self::BASE_SESS_PARAM + 0x003);
    const SESS_SESSION_EVENTS=(self::BASE_SESS_PARAM + 0x008);
    const SESS_USER_KEY=(self::BASE_SESS_PARAM + 0x0002);
    const SESS_SERVICE=(self::BASE_SESS_PARAM + 0x000F);
    const SYSDB_CTRL=IGK_KEY_SYSDB_CTRL;
	const GLOBALVARS = (self::BASE_SESS_PARAM + 0x0100);
    private $m_instances;
    private $m_sessionParams;
    ///<summary></summary>
    ///<param name="App"></param>
    ///<param name="params" ref="true"></param>
    /**
    * 
    * @param mixed $App
    * @param mixed * $params
    */
    public function __construct(& $params){
        $this->m_sessionParams=& $params;   
    }
    /**
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
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
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
    public function __isset($key){
        return isset($this->m_sessionParams[$key]);
    }
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
    ///<summary></summary>
    ///<param name="key"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $key
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
    */
    public function __toString(){
        //+ ASS: Appliation session storage
        return get_class($this)."[::ASS]";
    }
    ///<summary></summary>
    /**
    * 
    */
    private function _onUserChanged(){
        igk_invoke_session_event(__CLASS__."::UserChanged", array($this, null));
    }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    /**
    * 
    * @param mixed $obj
    * @param mixed $method
    */
    public function addInitializeSessionEvent($obj, $method){}
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    /**
    * 
    * @param mixed $obj
    * @param mixed $method
    */
    public function addUserChangedEvent($obj, $method){}
    ///<summary></summary>
    ///<param name="key"></param>
    /**
    * 
    * @param mixed $key
    */
    public function Clear($key){
        if(isset($this->m_sessionParams[$key]))
            unset($this->m_sessionParams[$key]);
    }
    ///<summary>register a custom uri compoent to reg uris</summary>
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
    ///<summary>create instance item for session</summary>
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
    ///<summary></summary>
    /**
    * 
    */
    public function generateCref(){
        $cref = igk_create_cref();
		igk_app()->getSettings()->{IGK_FORM_CREF} = $cref; 
        return $cref;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getApp(){
        return igk_app();
    }
    ///<summary>get controller params</summary>
    /**
    * get controller params 
    */
    public function & getControllerParams(){
        $p = & igk_app()->settings->appInfo->ctrlParams;
        return $p;
    }
	///<summary>store here general form setting</summary>
	public function getForm(){
		if (!igk_app()->settings->form){
			igk_app()->settings->form = (object)[];
		}
		return igk_app()->settings->form;
	}
    ///<summary></summary>
    /**
    * 
    */
    public function getCRef(){   
        $cref= igk_app()->settings->{IGK_FORM_CREF} ?? (function(){    
            return $this->generateCref();
        })(); 
        return $cref;
    }
    ///<summary></summary>
    ///<return refout="true"></return>
    /**
    * 
    * @return mixed|array params
    */
    public function & getData(){
        return $this->m_sessionParams;
    }
    public function NoStore($data){
        return $this->m_sessionParams === $data;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDomain(){
        return $this->getParam(self::SESS_DOMAIN);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getDomainBaseDir(){
        return $this->getParam(self::IGK_DOMAINBASEDIR_SESS_PARAM);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getdomainBaseFile(){
        $c= $this->getParam(self::SESS_DOMAIN_BASEFILE);
        if  ($c){
            $c = igk_dir(str_replace("%basepath%", igk_io_basedir(), $c));            
        }
        return $c;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getEvents(){
        return $this->getParam(self::SESS_SESSION_EVENTS);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getLang(){
		$g = igk_app()->getSettings()->{self::SESS_LANG_KEY};
        return $g ??  R::GetDefaultLang();
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getPageFolder(){
        if($p=$this->getParam(self::SESS_PAGEFOLDER_KEY)){
            return $p;
        }
        return IGK_HOME_PAGEFOLDER;
    }
    ///<summary>session get parameter</summary>
    ///<param name="key"></param>
    ///<param name="default" default="null"></param>
    /**
    * 
    * @param mixed $key
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
    ///<summary></summary>
    /**
    * 
    */
    public function getParamKeys(){
        return array_keys($this->m_sessionParams);
    }
    ///<summary></summary>
    /**
    * 
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
    ///<summary></summary>
    ///<param name="classname"></param>
    ///<return refout="true"></return>
    /**
    * 
    * @param mixed $classname
    * @return mixed|array controller parameters
    */
    public function & getRegisteredControllerParams($classname){
        igk_trace();
        igk_wln_e("data .... ");
        $g=null;
		$t = & $this->getControllerParams();
		if (isset($t[$classname]))
			$g = & $t[$classname];       
        return $g;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getUser(){
        return $this->getParam(self::SESS_USER_KEY);
    }
    ///<summary></summary>
    /**
    * 
    */
    public function getUserChangedEvent(){
        return $this->m_UserChangedEvent;
    }
    ///<summary></summary>
    ///<param name="app"></param>
    /**
    * 
    * @param mixed $app
    */
    public function initalize($app){
        if($app == $this->m_igk){
            $this->m_initializeSessionEvent->Call($this, null);
        }
    }
    ///<summary></summary>
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
    ///<summary></summary>
    ///<param name="classname"></param>
    ///<param name="tab" ref="true"></param>
    /**
    * 
    * @param mixed $classname
    * @param mixed * $tab
    */
    // public function registerControllerParams($classname, & $tab){
	// 	$p = & $this->getControllerParams();
	// 	$p[$classname] = & $tab;
    // }
	//  public function unregisterControllerParams($classname){
	// 	$p = & $this->getControllerParams();
	// 	unset($p[$classname]);
    // }
    ///<summary></summary>
    ///<param name="obj"></param>
    ///<param name="method"></param>
    /**
    * 
    * @param mixed $obj
    * @param mixed $method
    */
    public function removeInitializeSessionEvent($obj, $method){}
    ///<summary>reset param </summary>
    /**
    * reset param 
    */
    public function resetParam(){
        $this->m_sessionParams=array();
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setDomain($v){
        $this->setParam(self::SESS_DOMAIN, $v);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setDomainBaseDir($v){
        $this->setParam(self::IGK_DOMAINBASEDIR_SESS_PARAM, $v);
    }
    ///<summary></summary>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $v
    */
    public function setdomainBaseFile($v){
        $bpth = igk_io_basepath($v);
        if  ($bpth){
            $v = "%basepath%/".$bpth;
        }
        return $this->setParam(self::SESS_DOMAIN_BASEFILE, $v);
    }
    ///<summary></summary>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $value
    */
    public function setEvents($value){
        $this->setParam(self::SESS_SESSION_EVENTS, $value);
    }
    ///<summary></summary>
    ///<param name="lang"></param>
    /**
    * 
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
    ///<summary></summary>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $value
    */
    public function setPageFolder($value){
        $this->setParam(self::SESS_PAGEFOLDER_KEY, $value);
    }
    ///<summary>set session param</summary>
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
    ///<summary></summary>
    ///<param name="name"></param>
    ///<param name="value"></param>
    /**
    * 
    * @param mixed $name
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
    ///<summary></summary>
    ///<param name="user"></param>
    ///<param name="context"></param>
    /**
    * 
    * @param mixed $user
    * @param mixed $context
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

    public function getServices(){
        return $this->getParam(self::SESS_SERVICE);
    }
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
     * @param mixed $value 
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
}