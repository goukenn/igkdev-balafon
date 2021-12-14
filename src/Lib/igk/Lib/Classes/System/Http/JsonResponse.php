<?php
 

namespace IGK\System\Http;

use IGK\Helper\Utility;

class JsonResponse extends RequestResponse{
    var $data;
    var $headers = ["Content-Type:application/json"];
    public function __construct($data, $code=200)
    {
        $this->data = $data;
        $this->code = $code;
    }
    public function render(){
        $n = $this->data;
        $s = "";
        if ( ($is_obj = is_object($n)) || is_array($n)){
            if ($is_obj && method_exists($n, "to_json")){
                $s = $n->to_json(); 
            }else{
                $s = Utility::To_JSON($n); 
            }
        } else if (is_string($n)){
            $s = $n;
        }
        igk_wl($s); 
    }
}