<?php
// @author: C.A.D. BONDJE DOUE
// @filename: JsonResponse.php
// @date: 20220803 13:48:55
// @desc: 

 

namespace IGK\System\Http;

use Exception;
use IGK\Helper\Utility;

/**
 * represent request response
 * @package IGK\System\Http
 */
class JsonResponse extends RequestResponse{
    var $data;
    var $headers = ["Content-Type:application/json"];
    var $ignore_empty = true;
    public function __construct($data, $code=200)
    {
        $this->data = $data;
        $this->code = $code;
    }
    /**
     * render json response
     * @return void 
     * @throws Exception 
     */
    public function render(){
        $n = $this->data;
        $s = "";
        if ( ($is_obj = is_object($n)) || is_array($n)){
            if ($is_obj && method_exists($n, "to_json")){
                $s = $n->to_json(); 
            }else{
                $s = Utility::To_JSON($n, (object)[
                    "ignore_empty"=>$this->ignore_empty
                ]); 
            }
        } else if (is_string($n)){
            $s = $n;
        }
        igk_wl($s); 
    }
}