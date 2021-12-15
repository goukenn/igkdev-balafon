<?php
 

namespace IGK\System\Http;

use IGKCaches;
use IGKHtmlDoc;

/**
 * represent a web rendering result
 * @package IGK\System\Http
 */
class WebResponse extends RequestResponse{
    private $node;

    public $headers = [
        "Content-Type: text/html"
    ];

    public function __construct($node, $code=200, $header=null){
        $this->code = $code; 
        $this->node = $node;
        if ($header){
            $this->headers = $header;
        }
    }
    public function render() { 
        if (is_string($this->node)){
            igk_wl($this->node);
            return;
        } 
        if (is_object($this->node)){
            if (method_exists($this->node, "renderAJX")){
                $this->node->renderAJX();
                return;
            }
        }
    }
    public function output(){
 
        ob_start();
        $cache = is_object($this->node) &&  igk_is_class_instance_of($this->node, IGKHtmlDoc::class);
        $this->render();
        $s = ob_get_clean();
     

        $zip = 0 && igk_server()->accepts(["gzip"]);
        if ($cache){ 
            // ----------------------------------------------------------------
            // CACHE THE DOCUMENT URI
            // ----------------------------------------------------------------
            $option = $zip ? "_zip" : "";
            $file = IGKCaches::page_filesystem()->getCacheFilePath(igk_server()->REQUEST_URI.$option); 
            if ($zip){
                $s = igk_zip_output($s, 0, 1);
            }
            igk_io_w2file(
                $file, 
                $s);
            if ($zip){
                echo $s;
                igk_exit();
            }
        }
 
        if ($zip){    
            igk_zip_output($s);
            return $s;           
        }
        igk_set_header($this->code, $this->getStatus($this->code), $this->headers);
        echo $s;
        igk_exit();
    }
}