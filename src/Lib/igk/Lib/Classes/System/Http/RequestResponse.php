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
abstract class RequestResponse extends Response implements IInjectable{
 
    const RESPONSE_CODE_401_UNAUTHORIZED= 401;
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
    protected function _setHeader(){             
        if ($this->headers && count($this->headers)>0)
            $this->_treat_header();
  
        igk_set_header($this->code, self::GetStatus($this->code), $this->headers);  
    }
    abstract function render();

    protected function _treat_header(){
        $tab = [];
        array_map(function($a) use (& $tab){
            $m = explode(":", $a);
            if (count($m)>1){
                $tab[$m[0]] = $a;
            }else{
                $tab[$a]=$a;
            }
        }, $this->headers);
        $this->headers = array_values($tab);
    }
    public function cache_output($second){
        $ts=gmdate("D, d M Y H:i:s", time() + $second). " GMT";
        $this->headers[] = ("Expires: {$ts}");
        $this->headers[] = ("Pragma: cache");
        $this->headers[] = ("Cache-Control: max-age={$second}, public");
    }
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

    protected function __construct()
    {
        $this->status = self::GetStatus($this->code);
    }
    /**
     * 
     * @return object 
     * @throws IGKException 
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