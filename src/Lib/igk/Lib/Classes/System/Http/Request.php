<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Request.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Http;
 
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Console\ServerFakerInput;
use IGK\System\IInjectable;
use IGK\System\Security\Web\Traits\ContentSecurityManagementTrait;
use IGKException;

///<summary>request </summary>
/**
 * 
 * @package IGK\System\Http
 */
class Request implements IInjectable
{
    use ContentSecurityManagementTrait;
    
    static $sm_instance;
    private $m_params;
    private $js_data;
    private $m_header_data;
    private $m_query_info;
    /**
     * prepared request information
     * @var mixed
     */
    private $prepared;
    public function __debugInfo()
    {
        return null;
    }
    public function setJsonData(?string $data){
        igk_environment()->set('FakerInput', $data ? new ServerFakerInput($data) : null);
    }
    public function getUploadedData(){
        if (!$this->prepared){
            $this->js_data = igk_io_get_uploaded_data(); 
        }  
        return $this->js_data;
    }
    /**
     * prepare and return the updload data as json object
     * @return null|object|array
     */
    public function getJsonData(){
        $this->getUploadedData();
        if ($this->js_data !== null){
            //try to convert json data => data;
            return json_decode($this->js_data);
        } 
        return $this->js_data;
    }

    public function getFormData(){
        $ob = (object)$_REQUEST;
        return $ob;
    }
  
    /**
     * set the request parameters
     */
    public function setParam($params)
    {
        $this->m_params = $params;
    }
    /**
     * get the set parameters
     */
    public function getParam($id = null, $default = null)
    {
        if ($id !== null) {
            return igk_getv($this->m_params, $id, $default);
        }
        return $this->m_params;
    }

    public function getParams(){
        return $this->m_params;
    }

    ///<summary>base request instance</summary>
    /**
     * base request instance
     * @return  Request
     */
    public static function getInstance()
    {
        if (self::$sm_instance === null)
            self::$sm_instance = new self();
        return self::$sm_instance;
    }
    public function requestEntry(){
        $b = igk_server()->REQUEST_URI; 
        if (!$b)
            return null;
        $file = (($g = igk_server()->SCRIPT_NAME) ? $g : igk_server()->PHP_SELF);
        $dfile = implode("/", [rtrim(igk_io_rootdir(),"/"), ltrim($file, "/")]);
        if (!$dfile || !file_exists($dfile)){
            // // igk_ilog("entry request file is missing.");
            // igk_trace(); 
            igk_die("Misconfiguration: Entry request is missing. $dfile \n");
        }
        $t = IGKString::Uri(dirname($file));
        $s = $b;
        if (strstr($b, $t)) {
            $s = "/" . ltrim(substr($b, strlen($t)), "/");
        } 
        return urldecode($s);
    }
    private function __construct()
    {
    }
    /**
     * get option header 
     * @return HeaderData 
     */
    public function getHeader(){
        if (is_null($this->m_header_data)){
            $this->m_header_data = new HeaderData(igk_get_allheaders());
        }
        return $this->m_header_data ;
    }
    /**
     * get the request value
     * @param mixed $name 
     * @param mixed|null $default 
     * @return mixed 
     */
    public function get($name, $default = null)
    {
        return igk_getr($name, $default);
    }
    public function getBase64($name, $tab=null){
        if ($tab === null){
            $tab = $_REQUEST;
        }
        if (key_exists($name, $tab)){
            return base64_decode($tab[$name]);
        }
        return null;
    }
    /**
     * get arg from request
     * @param mixed $name 
     * @param mixed|null $default 
     * @return mixed 
     */
    public function have($name, $default=null){
        if (key_exists($name, $_REQUEST)){
            return igk_getr($name, $_REQUEST);
        }
        return  $default;
    }
    /**
     * 
     * @param mixed $type 
     * @return mixed 
     */
    public function method($type)
    {
        return igk_server()->method($type);
    }
    /**
     * get the file
     * @return void 
     */
    public function file($name)
    {
        return igk_getv($_FILES, $name);
    }

    public function view_args($params=null, $default=null)
    {
        $t = igk_get_view_args();
        if ($params!==null) 
        {
            return igk_getv($t, $params, $default);
        }
        return $t;
    }
    public function __toString()
    {
        return json_encode($this);
    }
    public function __get($name){
        if (!array_key_exists($name, $_REQUEST)){ 
            igk_environment()->isDev() && igk_ilog(sprintf("key %s not present", $name ));
            return null;           
        }
        return $this->get($name);
    }
    private function getQueryInfo(){
        if (is_null($this->m_query_info)){
            $inf = igk_io_query_info();
            $v_eu = $inf->entryuri;
            $pos = strpos($v_eu, ';');
            $inf->options = $pos !== false ? 
            igk_get_query_options(substr($inf->entryuri, $pos+1)) : [];
 
            $this->m_query_info = $inf;
        }
        return $this->m_query_info;
    }
    /**
     * get request query options 
     * @param string $key to resolv
     * @param mixed $default 
     * @return mixed
     */
    public function option(string $key, $default=null){
        $inf = $this->getQueryInfo(); 
        return igk_getv($inf->options, $key, $default); 
    }
    /**
     * get file info helper
     * @param string $key 
     * @return null|array 
     * @throws IGKException 
     */
    public function getFile(string $key):?array{
        if (isset($_FILES)){
            return igk_getv($_FILES, $key);
        }
        return null;
    }
    public function  moveUploadedFile($name, $destination, ?string $requestType=null):?bool{
        if ($file = $this->getFile($name)){
            if (($file['size'] == 0) || ($requestType && ($requestType!= $file['type']))){
                return false;
            } 
            return igk_io_move_uploaded_file($file['tmp_name'], $destination);             
        }
        return false;
    }   
}
