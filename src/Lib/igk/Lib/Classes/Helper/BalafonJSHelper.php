<?php
namespace IGK\Helper;

class BalafonJSHelper{
    /**
     * get post data js expression
     * @param mixed $data 
     *   $data = [
     *      "uri" => uri of the 
     *   ]
     * @return void 
     */
    public static function post($data){
        return "ns_igk.ajx.post(".json_encode($data).");";
    }
}