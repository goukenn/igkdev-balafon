<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ErrorRequestResponse.php
// @date: 20220803 13:48:55
// @desc: 



namespace IGK\System\Http;

use IGK\Helper\JSon as HelperJSon;
use IGK\System\Html\Dom\HtmlDocTheme; 

use function igk_resources_gets as __;

class ErrorRequestResponse extends RequestResponse{
    var $type = "json";
    var $code = RequestResponseCode::BadRequest;
    var $message;
    public function __construct($code, $message=null, $headers=null){
        $this->code = $code;
        $this->message = $message;
        $headers = $headers ?? \IGK\System\Http\Helper\Response::GetHeaderOptions(igk_server()->REQUEST_METHOD);
        $this->headers = $headers;
    }

    protected function _setHeader(){
        parent::_setHeader();
    }
    public function render(){ 
        $obj = ["response"=>(object)[
            "code"=>$this->code,
            "status"=> $this->code== 200? 'OK' :  self::GetStatus($this->code),
            "message"=>$this->message
        ]];
        switch($this->type){
            case "json":        
            if (igk_server()->accept($this->type)){
                \igk_header_set_contenttype($this->type);
                return HelperJSon::Encode($obj, (object)['ignore_empty'=>1, 'ignore_null'=>1]); //  json_encode($obj);
            }
            break;
            default:
                igk_header_set_contenttype("html");
            break;
        }
        $doc = igk_create_node("html");        
        $doc->add("head")->addObData(function($c){ 
            $c->add('meta')->setAttributes(['charset'=>'utf8']);
            $c->add("title")->Content = __("Error");
            $c->addStyle()->setAttribute("type", "text/css")->Content  = $this->getErrorStyle();

        },null); 
        $doc->add('body')->setClass("error_doc")->addObData(function($c)use($obj){
            $n = $c->add("div");
            (new ResponseHtmlRenderer($n,$obj))->render();
        });
        return "<!DOCTYPE html>".$doc->render();
        
    }
    public function getErrorStyle(){        
        $theme = new HtmlDocTheme(null, -1);
        $theme["*, html, body"] = "margin:0px; padding:0px;";
        $theme->bindFile(IGK_LIB_DIR."/Styles/error_request.pcss");
        return $theme->get_css_def();
    }
}