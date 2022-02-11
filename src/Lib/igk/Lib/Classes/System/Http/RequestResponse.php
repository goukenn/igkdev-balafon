<?php


namespace IGK\System\Http;

abstract class RequestResponse extends Response{
 
    /**
     * return code
     */
    var $code = 200;
    /**
     * additinal header
     */
    var $headers; 

     
    public static function GetStatus($code){
        return igk_getv(
            [
                "404"=>"Page Not found", 
                "400"=>"Bad request!!!", 
            ],
            $code
        );
    }
    /**
     * output the current response
     * @return void 
     */
    public function output(){ 
        igk_set_header($this->code, self::GetStatus($this->code), $this->headers); // "testing base", $headers);
        igk_wl($this->render());
        igk_exit();
    }
    abstract function render();

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
     * @param string $type 
     * @param mixed $data 
     * @param int $code 
     * @param null|array $headers 
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
}