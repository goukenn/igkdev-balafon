<?php
// @author: C.A.D. BONDJE DOUE
// @filename: Request.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Http;
use Exception;
use IGK\Helper\IO;
use IGK\Helper\StringUtility as IGKString;
use IGK\System\Console\ServerFakerInput;
use IGK\System\Html\WebHearderConstants;
use IGK\System\IInjectable;
use IGK\System\Security\Web\Traits\ContentSecurityManagementTrait;
use IGKException;
/**
 * 
 * @package IGK\System\Http
 */
class Request implements IInjectable, IContentSecurityProvider
{
    use ContentSecurityManagementTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    const REQUEST_JSON_DATA_ENV_KEY = 'RequestFakeJsonInput';

    /**
    * auto generate doc.
    * @var mixed
    */
    const FILES_FIELD = "\$files";

    /**
    * auto generate doc.
    * @var mixed
    */
    const ARRAY_RESPONSE_CODE = '@__response_code';

    /**
    * auto generate doc.
    * @var mixed
    */
    const QUERY_OPTIONS = 'query_options';

    /**
     * 
     * @param mixed $args 
     * @return string 
     */

    public static function glueActionRequestArgument($args){
        return '/'.implode('/', array_filter(array_map(function($a){
            if (is_string($a) || is_numeric($a))return $a;
            return null;
        }, $args)));
    }
    /**
     * support form data file request
     * @param mixed $data 
     * @return bool 
     */

    public static function IsSupportFileRequest($data){
        return isset( ((object)$data)->{self::FILES_FIELD});
    }
    /**
     * 
     * @var self
     */
    private static $sm_instance;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_params;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $js_data;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_header_data;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_query_info;
    /**
     * prepared request information
     * @var mixed
     */
    private $prepared;

    /**
    * Used by var_dump() to customize debug output.
    */
    public function __debugInfo()
    {
        return null;
    }
    /**
     * store json data 
     * @param null|string $data 
     * @return mixed old data 
     */

    public function setJsonData(?string $data){
        $env = igk_environment();
        $d = $env->get($k = self::REQUEST_JSON_DATA_ENV_KEY);
        $env->set($k, $data ? new ServerFakerInput($data) : null);
        return $d;
    }
    /**
     * retrieve up loaded data
     * @return ?string 
     */

    public function getUploadedData(){
        if (!$this->prepared){
            $this->js_data = igk_io_get_uploaded_data(); 
        }  
        return $this->js_data;
    }
    /**
     * check this current request support form data
     * @return bool 
     */

    public function isFormData():bool{
        return empty($this->js_data) && 
        (igk_server()->CONTENT_TYPE != 'application/json');
    }
    /**
     * do response with data
     * @param mixed|Response|array|object $data response data.
     * @return mixed 
     */

    public function response($data){
        return igk_do_response($data);
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
    /**
     * transform global request data request object
     * @return object 
     */

    public function getFormData(){
        $ob = (object)$_REQUEST;
        if ($_FILES && (count($_FILES)>0)){
            $ob->{self::FILES_FIELD} = $_FILES;
        }
        return $ob;
    }
    /**
     * 
     * @param mixed $key 
     * @return bool 
     */

    public function isset($key){
        return isset($_REQUEST[$key]);
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

    /**
    * auto generate doc.
    */
    public function getParams(){
        return $this->m_params;
    }
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
    /**
     * request view
     * @return bool 
     */

    public function requestView(): bool{

        if ($q = igk_getv($this->getQueryInfo(), self::QUERY_OPTIONS)){
            $r = (igk_getv($q, 'fmt') == 'html') || 
                (igk_getv($q, 'render') == 'web');
            return $r;
        } 
        return false;
    }
    /**
     * parse current query options 
     * @return string 
     */

    public function parseOptions(?bool $full=false):string{
        $s = '';
        if ($q = igk_getv($this->getQueryInfo(), self::QUERY_OPTIONS)){
            $s = implode(";", array_map(function($v, $k){
                return implode("=",[$k,$v]);
            }, $q, array_keys($q)));
            if ($full){
                return ';'.$s;
            }
        }
        return $s;
    }
    /**
     * 
     * @return null|string 
     * @throws Exception 
     */

    public function requestEntry(){
        $v_srv = igk_server();
        $b = $v_srv->REQUEST_URI; 
        if (!$b)
            return null;
        $file = (($g = $v_srv->SCRIPT_NAME) ? $g : $v_srv->PHP_SELF);
        if (preg_match('/[~]/', $file)){
            igk_die("request entry not allowed");
        } 
        $dfile = implode("/", [rtrim(igk_io_rootdir(),"/"), ltrim($file, "/")]);
        if (!$dfile || !igk_io_file_exists($dfile,true)){
            // // igk_ilog("entry request file is missing.");
            // igk_trace(); 
            igk_die("Misconfiguration: Entry request is missing [". $dfile ."] - RequestURI : {$b} " .'\n');
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

    /**
    * auto generate doc.
    * @param mixed $name
    * @param null|mixed $tab
    */
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

    /**
    * auto generate doc.
    * @param null|mixed $params
    * @param null|mixed $default
    */
    public function view_args($params=null, $default=null)
    {
        $t = igk_get_view_args();
        if ($params!==null) 
        {
            return igk_getv($t, $params, $default);
        }
        return $t;
    }

    /**
    * get string presentation.
    */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
    * .destructor
    * @param mixed $name
    */
    public function __get($name){
        if (!array_key_exists($name, $_REQUEST)){ 
            igk_environment()->isDev() && igk_ilog(sprintf("key %s not present", $name ));
            return null;           
        }
        return $this->get($name);
    }
    /**
     * get query option
     * @return mixed|IGK\System\Http\IQueryInfoOptions
     * @throws IGKException 
     */

    public function getQueryInfo(){
        if (is_null($this->m_query_info)){
            $inf = igk_io_query_info();
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
    /**
     * move uploaded file to 
     * @param string $name 
     * @param string $destination 
     * @param null|string $requestType 
     * @return null|bool 
     */

    public function  moveUploadedFile(string $name, string $destination, ?string $requestType=null):?bool{
        if ($file = $this->getFile($name)){
            if (($file['size'] == 0) || ($requestType && ($requestType!= $file['type']))){
                return false;
            } 
            return igk_io_move_uploaded_file($file['tmp_name'], $destination);             
        }
        return false;
    }   
    /**
     * create an error message data
     * @param string $message 
     * @return array 
     */

    public function error(string $message, ?int $code=null):array{
        $t = ['error'=>true, 'message'=>$message];
        $t[self::ARRAY_RESPONSE_CODE] = $code ?? RequestResponseCode::BadRequest;         
        return $t;
    }
    /**
     * 
     * @return bool 
     */

    public function isRestRequest():bool{
        if ($this->getHeader()->{WebHearderConstants::igk_web_response} == 1){            
            return false;
        }
        if ($this->isAjx() || $this->sendsJSon()){
            return true;
        }
        return false;
    }
    /**
     * 
     * @return bool 
     */

    public function isAjx():bool{
        return igk_is_ajx_demand();
    }

    /**
    * auto generate doc.
    * @return bool
    */
    public function sendsJSon():bool{
        $h = $this->getHeader();
        if ($t = $h->{'content_type'}){
            return explode(';', $t, 1)[0] == 'application/json';
        }
        return false;
    }
    /**
     * 
     * @return bool 
     */

    public function isWebRequest():bool{
        return !$this->isRestRequest();
    }
}