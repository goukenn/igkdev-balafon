<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKServer.php
// @date: 20220803 13:48:54
// @desc: server info

namespace IGK;
use IGK\Helper\StringUtility;
use IGK\System\DataArgs;
use IGK\System\Security\Web\HeaderAccessObject;

///<summary>represent server management </summary>
/**
* represent server management
* @property string $root_dir system root directory
* @property string $full_request_uri system fullrequest uri
* @property string $HTTP_ACCEPT_ENCODING server HTTP_ACCEPT_ENCODING
* @property string $REQUEST_URI server REQUEST_URI
* @property string $REQUEST_METHOD server REQUEST_METHOD
* @property string $DOCUMENT_ROOT server DOCUMENT_ROOT
* @property string $SCRIPT_FILENAME server SCRIPT_FILENAME
* @property string $HTTP_HOST server HTTP_HOST
* @property string $HTTP_IGK_AJX to detect ajx demand
* @property string $HTTP_IGK_AJX_APP to detect application that request ajx demand
* @property string $HTTP_USER_AGENT server user agent
* @property bool $IS_WEBAPP to detect application that request ajx demand
*/
final class Server{
    private $data;  
    private $m_access_control;
    private $m_access_object;
    private static $sm_server;

    /**
     * get if server request in access control
     * @return ?bool 
     */
    public function getAccessControl(){
        return $this->m_access_control;
    }
    /**
     * access data object
     * @return null|HeaderAccessObject 
     */
    public function getAccessObject():?HeaderAccessObject{
        return $this->m_access_object;
    }
    ///<summary></summary>
    /**
     * 
     */
    public static function IsIGKDEVSERVER() : bool{
        $r= self::getInstance()->HTTP_USER_AGENT;
        if(strstr($r, IGK_SERVERNAME)){
            return true;
        }
        return false;
    }
    ///get if this server runing on the loal server
    public static function IsLocal(){
        $v_saddr=self::ServerAddress();
        $v_srddr=self::RemoteIp();
        $v=($v_srddr == "::1") || ($v_saddr == $v_srddr) || ($v_saddr && preg_match("/^127\.(.)/i", $v_saddr));
        return $v;
    }
    ///<summary>get remote ip</summary>
    /**
     * get remote ip
     * @return mixed 
     */
    public static function RemoteIp(){
        return self::getInstance()->REMOTE_ADDR;
    }
    ///<summary></summary>
    public static function ServerAddress(){
        return self::getInstance()->SERVER_ADDR;
    }
    ///<summary></summary>
    /**
    * 
    */
    private function __construct(){ 
        $this->prepareServerInfo();
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __get($n){
        if(isset($this->data[$n]))
            return $this->data[$n];
        return null;
    }
    /**
     * get encoding support
     */
    public function accepts($list){
        $accept = $this->HTTP_ACCEPT_ENCODING;
        if (is_array($list) && !is_null($accept)){
            foreach($list as $k){
                if (strstr($accept, $k)){
                    return true;
                }
            }
        }
        return false;
    }
    ///<summary></summary>
    ///<param name="n"></param>
    /**
    * 
    * @param mixed $n
    */
    public function __isset($n){
        return isset($this->data[$n]);
    }
    ///<summary></summary>
    ///<param name="n"></param>
    ///<param name="v"></param>
    /**
    * 
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if ($n == "REQUEST_STRING"){
            igk_wln_e("try change request uri ", $v);
        }
        if($v === null){
            unset($this->data[$n]);
        }
        else
            $this->data[$n]=$v;
    }
    ///<summary>return if server accept return type</summary>
    public function accept($type="html"){
        static $accept_type= null;
        if ($accept_type===null){
            $accept_type = [
                "html"=>"text/html",
                "json"=>"application/json"
            ];
        }
        $v_accept = $this->HTTP_ACCEPT ?? '';
        $a = explode(",", $v_accept);
        if (in_array("*/*", $a)){
            return true;
        }
        $mtype = igk_getv($accept_type, $type, null);
        return $mtype && in_array($mtype, $a);
    }

    public function get($name, $default=null){
        return igk_getv($this->data, $name, $default);
    }
    ///<summary>get server instance</summary>
    /**
    * @return Server
    */
    public static function getInstance(){
        if (self::$sm_server ===null){
            self::$sm_server = new self();
        }
        return self::$sm_server;
    }
    ///<summary></summary>
    ///<param name="file"></param>
    /**
    * 
    * @param mixed $file
    */
    public function IsEntryFile($file){
        return $file === realpath($this->SCRIPT_FILENAME);
    }
    ///<summary>check if this request is POST</summary>
    /**
    * check if this request is POST
    */
    public function ispost(){
        return $this->REQUEST_METHOD == "POST";
    }
    ///<summary>check for method. if type is null return the REQUEST_METHOD</summary>
    /**
    * check for method
    */
    public function method($type=null){
			if ($type===null)
				return $this->REQUEST_METHOD;
        return $this->REQUEST_METHOD == $type;
    }
    public function isMultipartFormData(){
        return strpos($this->CONTENT_TYPE, "multipart/form-data") === 0;
    }
    /**
     * @return ?string
     */
    public function script_dir(){
        if ($f = $this->SCRIPT_FILENAME){
            return dirname($f);
        }
        return null;
    }
    ///<summary></summary>
    /**
    * 
    */
    public function prepareServerInfo(){
    
        $this->data=array();
        foreach($_SERVER as $k=>$v){          
            $this->data[$k]=$v;
        }
        $headers = igk_get_allheaders();

        if ($headers  && isset($headers['ACCESS_CONTROL_REQUEST_METHOD'])){
            $this->m_access_control = 1;
            $v_access_object =  [
                'method'=>$headers['ACCESS_CONTROL_REQUEST_METHOD'],
                'headers'=>$headers['ACCESS_CONTROL_REQUEST_HEADERS'],
                'authorization'=>igk_getv($headers, 'AUTHORIZATION'),
                'origin' => igk_getv($headers, 'ORIGIN'),
            ];
            $this->m_access_object = HeaderAccessObject::ActivateNew($v_access_object);
        }
        // + header 
        error_log(json_encode(compact('headers')));

        error_log(json_encode($_SERVER));


        $this->IGK_SCRIPT_FILENAME=StringUtility::Uri(realpath($this->SCRIPT_FILENAME));
        $this->IGK_DOCUMENT_ROOT= StringUtility::Uri(realpath($this->DOCUMENT_ROOT))."/";
        $sym_root=$this->IGK_DOCUMENT_ROOT !== $this->DOCUMENT_ROOT;
        $c_script=$this->IGK_SCRIPT_FILENAME;

        
        if(!$sym_root)
            $c_script=$this->SCRIPT_FILENAME;
        if(!empty($doc_root=$this->IGK_DOCUMENT_ROOT)){
            $doc_root=str_replace("\\", "/", realpath($doc_root));
            $self=substr($c_script, strlen($doc_root));
            if((strlen($self) > 0) && ($self[0] == "/"))
                $self=substr($self, 1);
            $basedir=str_replace("\\", "/", dirname($doc_root."/".$self));
            $this->IGK_BASEDIR=$basedir;
            $uri=$this->REQUEST_SCHEME."://".$this->HTTP_HOST;
            $query=substr($basedir, strlen($doc_root) + 1);
            if(!empty($query))
                $query .= "/";
            $baseuri=$uri."/".$query;
            $this->IGK_BASEURI=$baseuri;
        }
        $this->IGK_CONTEXT=($t_=isset($this->HTTP_HOST)) ? "html": "cmd";
        $this->LF=$t_ ? "\n": "<br />";
        if(!empty($env=$this->ENVIRONMENT)){
            $this->ENVIRONMENT=defined('IGK_ENV_PRODUCTION') ? "production": $env;
        }
        else{
            $this->ENVIRONMENT=defined('IGK_ENV_PRODUCTION') ? "production": "development";
        }
        if(!isset($this->WINDIR)){
            $this->WINDIR=($this->OS == "Windows_NT");
        }
        if(isset($_SERVER['REDIRECT_STATUS']) && isset($_GET["__c"])){
            $_get=array_slice($_GET, 0);
            $this->REDIRECT_CODE=$_get["__c"];
            $this->REDIRECT_OPT=array();
            unset($_get["__c"]);
            $_SERVER["QUERY_STRING"]=http_build_query($_get);
        }
        $this->REQUEST_PATH = !empty(($ruri = $this->REQUEST_URI)) ? explode("?", $ruri)[0] :  "/";
     
        if  (empty($_SERVER['REQUEST_SCHEME']) && !igk_is_cmd()){
            $scheme = "http";
            if ($this->HTTPS == "on"){
               $scheme .= "s";
            }
            $this->REQUEST_SCHEME = $scheme;
        }
        $uri = $this->REQUEST_URI;

        $this->full_request_uri = !empty($uri) ? StringUtility::Uri(urldecode(rtrim(
            implode("/", array_filter([$this->GetRootUri(), ltrim($this->REQUEST_URI, '/')])), "/"))) : ""; 

        if (!empty($doc_root = $this->IGK_DOCUMENT_ROOT) || (defined('IGK_APP_DIR') && !empty($doc_root = constant('IGK_APP_DIR')))) {
            $doc_root = rtrim(StringUtility::Dir($doc_root), "/");
        }
        $this->root_dir = $doc_root;
         // + | internal stus code
        $this->STATUS_CODE = $this->REDIRECT_CODE ?? $this->REDIRECT_STATUS ?? $this->STATUS ?? 400;
        $this->IS_WEBAPP = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['DOCUMENT_ROOT']); 
    }
    public function GetRootUri($secured=false){
        // return "";

        if(!$secured && $this->is_secure())
            $secured=true;
        if($secured){
            $out='https://';
        }
        else{
            $out='http://';
        }
        $port="";
        if($c= $this->GetPort($secured)){
            $port=':'.$c;
        }
        $n= $this->SERVER_NAME;
        if(!empty($n))
            $out .= rtrim($n, '/').$port;
        if(!empty($uri))
            $out .= '/'.rtrim($uri, '/');
        $out=str_replace('\\', '/', $out);
        return $out;
    }
    public function GetPort($secure=false){
        $p= $this->SERVER_PORT;
        if(($secure) && ($p != 443) || (!$secure && ($p != 80)))
            return $p;
        return null;
    }

    public function is_secure(){
        return $this->HTTPS == "on";
    }
    ///<summary></summary>
    /**
    * 
    */
    public function to_array(){
        return $this->data;
    }

    public static function RequestTime(){
        $time = $_SERVER["REQUEST_TIME_FLOAT"];
        return (microtime(true) - $time);
    }
}
