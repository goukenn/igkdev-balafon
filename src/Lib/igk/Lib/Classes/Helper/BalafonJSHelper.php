<?php
// @author: C.A.D. BONDJE DOUE
// @filename: BalafonJSHelper.php
// @date: 20220803 13:48:57
// @desc: 

namespace IGK\Helper;

use IGKException;

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
    /**
     * stringify array option to js  presentation
     * @param array $options 
     * @return mixed 
     * @throws IGKException 
     */
    public static function Stringify(array $options){
            if (class_exists(\igk\js\common\JSExpression::class))
                $options = \igk\js\common\JSExpression::Stringify((object)$options);
            else{
                if (function_exists('igk_js_stringify'))
                    $options = call_user_func('igk_js_stringify', [$options]);
                else {
                    $options = json_encode((object)$options);
                }
            }
            return $options;
    }
}