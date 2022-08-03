<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonJSHelper.php
// @date: 20220803 13:48:57
// @desc: 

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