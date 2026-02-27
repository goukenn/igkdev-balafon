<?php
// @author: C.A.D. BONDJE DOUE
// @filename: IGKServer.php
// @date: 20220803 13:48:54
// @desc: server info
namespace IGK;
use IGK\Helper\StringUtility;
use IGK\System\Configuration\Controllers\SystemUriActionController;
use IGK\System\Http\AcceptMimeTypes;
use IGK\System\IToArray; 
use IGK\System\Security\Web\HeaderAccessObject; 
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
final class Server implements IToArray{

    /**
    * Property: data.
    * @var mixed
    */
    private $data;

    /**
    * Property: access control.
    * @var mixed
    */
    private $m_access_control;

    /**
    * Property: access object.
    * @var mixed
    */
    private $m_access_object;

    /**
    * Property: server.
    * @var mixed
    */
    private static $sm_server;
    /**
     * get if server request in access control
     * @return ?bool 
     */

    public function getAccessControl(){
        return $this->m_access_control;
    }
    /**
     * access-control data object
     * @return null|HeaderAccessObject 
     */

    public function getAccessObject():?HeaderAccessObject{
        return $this->m_access_object;
    }

    /**
    * auto generate doc.
    */
    public static function IsIGKDEVSERVER() : bool{
        $r= self::getInstance()->HTTP_USER_AGENT;
        if(strstr($r, IGK_SERVERNAME)){
            return true;
        }
        return false;
    }
    ///get if this server runing on the loal server

    /**
    * Returns true if Local.
    */
    public static function IsLocal(){
        $v_saddr=self::ServerAddress();
        $v_srddr=self::RemoteIp();
        $v=($v_srddr == "::1") || ($v_saddr == $v_srddr) || ($v_saddr && preg_match("/^127\.(.)/i", $v_saddr));
        return $v;
    }
    /**
     * get remote ip
     * @return mixed 
     */

    public static function RemoteIp(){
        return self::getInstance()->REMOTE_ADDR;
    }

    /**
    * Server address.
    */
    public static function ServerAddress(){
        return self::getInstance()->SERVER_ADDR;
    }
    /**
     * check server is url  encoded data
     * @return bool 
     */

    public function isURLEncoded(){
        return $this->CONTENT_TYPE == 'application/x-www-form-urlencoded';
    }
    /**
    * 
    */
    private function __construct(){ 
        $this->prepareServerInfo();
    }

    /**
    * auto generate doc.
    * @param mixed $n
    */

    public function __get($n){
        if(isset($this->data[$n]))
            return $this->data[$n];
        return null;
    }
    /**
     * check accepts encoding support
     * @param params hom
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

    /**
    * auto generate doc.
    * @param mixed $n
    */

    public function __isset($n){
        return isset($this->data[$n]);
    }

    /**
    * auto generate doc.
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

    /**
    * Accept.
    * @param mixed $type
    */
    public function accept($type="html"){
        static $accept_type= null;
        if ($accept_type===null){
            $accept_type = [
                "html"=>"text/html",
                "json"=>"application/json"
            ];
        }
        $v_accept = $this->HTTP_ACCEPT ?? '*/*';
        $a = explode(',', $v_accept);
        if (in_array("*/*", $a)){
            return true;
        }
        $mtype = igk_getv($accept_type, $type, null);
        return $mtype && in_array($mtype, $a);
    }

    /**
    * Returns.
    * @param mixed $name
    * @param null|mixed $default
    */
    public function get($name, $default=null){
        return igk_getv($this->data, $name, $default);
    }

    /**
    * auto generate doc.
    * @return Server
    */

    public static function getInstance(){
        if (self::$sm_server ===null){
            self::$sm_server = new self();
        }
        return self::$sm_server;
    }

    /**
    * Event stream request.
    */
    public function eventStreamRequest(){
        return $this->HTTP_ACCEPT == AcceptMimeTypes::EventStream;
    }

    /**
    * auto generate doc.
    * @param mixed $file
    */

    public function IsEntryFile($file){
        return $file === realpath($this->SCRIPT_FILENAME);
    }
    /**
    * check if this request is POST
    */

    public function ispost(){
        return $this->REQUEST_METHOD == "POST";
    }
    /**
    * check for method
    */

    public function method($type=null){
			if ($type===null)
				return $this->REQUEST_METHOD;
        return $this->REQUEST_METHOD == $type;
    }

    /**
    * Returns true if Multipart Form Data.
    */
    public function isMultipartFormData(){
        return strpos($this->CONTENT_TYPE, IGK_HTML_ENCTYPE) === 0;
    }

    /**
    * auto generate doc.
    * @return ?string
    */

    public function script_dir(){
        if ($f = $this->SCRIPT_FILENAME){
            return dirname($f);
        }
        return null;
    }
    /**
    * preparet server information 
    */

    public function prepareServerInfo(){
        $this->data=array();
        foreach($_SERVER as $k=>$v){          
            $this->data[$k]=$v;
        }
        if (is_null($this->REQUEST_METHOD)){
            $this->REQUEST_METHOD = 'GET';
        }
        $headers = igk_get_allheaders();
        // init authozation
        if ($headers  && 
                $this->_checkAccessHeader($headers))                
        {
            $this->m_access_control = 1;
            $v_access_object =  [
                'method'=>igk_getv($headers, 'ACCESS_CONTROL_REQUEST_METHOD', '*'),
                'headers'=>igk_getv($headers,'ACCESS_CONTROL_REQUEST_HEADERS', '*'),
                // PREFIX WIDTH - X_ for ovh server
                'authorization'=>igk_getvfirst_found($headers, ['AUTHORIZATION', 'X_AUTHORIZATION']),
                'origin' => igk_getv($headers, 'ORIGIN'),
            ];
            $this->m_access_object = HeaderAccessObject::ActivateNew($v_access_object);
        }
        // + header 
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
        // + | environment setting mo
        $v_envkey = 'IGK_ENVIRONMENT';
        if(empty($this->ENVIRONMENT) || defined($v_envkey)){ // force defined environment
            $this->ENVIRONMENT= defined($v_envkey) ? constant($v_envkey) : "development";
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
        $this->root_dir = realpath($doc_root);
         // + | internal stus code
        $this->STATUS_CODE = $this->REDIRECT_CODE ?? $this->REDIRECT_STATUS ?? $this->STATUS ?? 400;
        $this->IS_WEBAPP = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['DOCUMENT_ROOT']); 
    }
    /**
     * check weather access control required
     * @param mixed $headers 
     * @return bool 
     */
    private function _checkAccessHeader($headers){
        foreach(['AUTHORIZATION', 'X_AUTHORIZATION', 'ACCESS_CONTROL_REQUEST_METHOD', 'ORIGIN'] as $k){
            if (isset($headers[$k]) || isset($headers["X_".$k])){
                return true;
            }
        }
        return false;
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

    /**
    * Returns Port.
    * @param mixed $secure
    */
    public function GetPort($secure=false){
        $p= $this->SERVER_PORT;
        if(($secure) && ($p != 443) || (!$secure && ($p != 80)))
            return $p;
        return null;
    }

    /**
    * Returns true if secure.
    */
    public function is_secure(){
        return $this->HTTPS == "on";
    }

    /**
    * auto generate doc.
    */
    public function to_array(): ?array{
        return $this->data;
    }

    /**
    * Request time.
    */
    public static function RequestTime(){
        $time = $_SERVER["REQUEST_TIME_FLOAT"];
        return (microtime(true) - $time);
    }
    /**
     * get upload info
     * @var IGK\getUploadAJXInfo
     */

    public function getUploadAJXInfo(){
        $finfo = null;
		if (igk_is_ajx_demand()){
			$finfo = (object)[
				"name"=>igk_server()->HTTP_IGK_FILE_NAME,
				"size"=> igk_server()->HTTP_IGK_UP_FILE_SIZE,				
				"filetype"=>igk_server()->HTTP_IGK_UP_FILE_TYPE,
			];
		}
        return $finfo;
    }
    /**
     * retrieve the configuration path
     */

    public function getConfigurationPath():string{
        return SystemUriActionController::GetConfigurationPath();
    }

    /**
    * Returns Configuration Setting Path.
    * @return string
    */
    public function getConfigurationSettingPath():string{
        return sprintf('%s!settings', $this->getConfigurationPath());
    }
}