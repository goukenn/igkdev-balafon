<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RequestResponse.php
// @date: 20220728 15:51:46
// @desc: request reponse
namespace IGK\System\Http;
use IGK\Helper\Activator;
use IGK\System\IInjectable;
use IGKException;
/**
 * 
 * @package IGK\System\Http
 */

/**
* auto generate doc.
* @package IGK\System\Http
*/
abstract class RequestResponse extends Response implements IInjectable{

    /**
    * Constant: response code 401 unauthorized.
    * @var mixed
    */
    const RESPONSE_CODE_401_UNAUTHORIZED= 401;

    /**
    * Constant: response code 403 forbiden.
    * @var mixed
    */
    const RESPONSE_CODE_403_FORBIDEN = 403;
    /**
     * return code
     */
    var $code = 200;
    /**
     * additinal header
     */
    var $headers; 
    /**
     * get the status
     * @var mixed
     */
    var $status;

    /**
    * Returns Status.
    * @param mixed $code
    */
    public static function GetStatus($code){
        return  StatusCode::GetStatus($code);
    }
    /**
     * output the current response
     * @return void 
     */

    public function output(){ 
        $this->_setHeader();
        igk_wl($this->render());
        igk_exit();
    }

    /**
    * Set header.
    */
    protected function _setHeader(){             
        if ($this->headers && count($this->headers)>0)
            $this->_treat_header();
        igk_set_header($this->code, self::GetStatus($this->code), $this->headers);  
    }

    /**
    * Renders.
    */
    abstract function render();

    /**
    * Allow multiple header entry.
    * @param string $header_name
    */
    protected function _allow_multiple_header_entry(string $header_name){
        return in_array($header_name, ['Set-Cookie']);
    }

    /**
    * Treat header.
    */
    protected function _treat_header(){
        $tab = [];
        array_map(function($a) use (& $tab){
            $m = explode(":", $a);
            if ($this->_allow_multiple_header_entry($m[0])){
                $tab[] = $a;
                return;
            }
            if (count($m)>1){
                $tab[$m[0]] = $a;
            }else{
                $tab[$a]=$a;
            }
        }, $this->headers); 
        $this->headers = array_values($tab); 
    }

    /**
    * Cache output.
    * @param mixed $second
    */
    public function cache_output($second){
        $ts=gmdate("D, d M Y H:i:s", time() + $second). " GMT";
        $this->headers[] = ("Expires: {$ts}");
        $this->headers[] = ("Pragma: cache");
        $this->headers[] = ("Cache-Control: max-age={$second}, public");
    }

    /**
    * Clears headers.
    */
    public function clear_headers(){
        $this->headers = [];
    }
    /**
     * create a response
     * @param string $type type of the response json|web|xml
     * @param mixed $data data to serve as resonse
     * @param int $code status code 
     * @param null|array $headers extrat header
     * @return object 
     */

    public static function Create(?string $type, $data,int $code=200, ?array $headers=null){
        $cl = ($type)? __NAMESPACE__."\\".ucfirst($type)."Response" : null;
        if ($cl && class_exists($cl)){
            $obj = new $cl($data, $code, $headers);
        }else {
            $obj = new WebResponse($data, $code, $headers);
        }  
        return $obj;
    }

    /**
    * .ctr
    */
    protected function __construct()
    {
        $this->status = self::GetStatus($this->code);
    }

    /**
    * auto generate doc.
    * @return object
    */

    public static function CreateResponse(){
        $type = igk_getv(["application/json"=>"json"], igk_server()->CONTENT_TYPE);
        return self::Create($type, null, 200);
    }
    /**
     * create a json response
     * @param mixed $data 
     * @return object 
     */

    public function json($data){
        return static::Create(__FUNCTION__, $data);
    }

    /**
    * Download.
    * @param mixed $name
    * @param mixed $size
    * @param mixed $data
    * @param null|mixed $mimeType
    * @param mixed $encoding
    * @param mixed $exit
    */
    public function download($name, $size, $data, $mimeType=null, $encoding="binary", $exit=0){
        igk_download_content($name, $size, $data, $mimeType, $encoding, $exit);
    }
    /**
     * response with 
     * @param array $data of RequestResponseInfo::class 
     * @param string $type 
     * @return object 
     */

    public static function Response(array $data=[], $type='json'){
        $ref = Activator::CreateNewInstance(RequestResponseInfo::class, $data, true);
        return self::Create($type,
            $ref, $ref->code);
    }
}