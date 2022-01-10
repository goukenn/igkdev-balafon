<?php
 

namespace IGK\System\Http;

use IGK\System\Html\HtmlRenderer;
use IGKCaches;
use IGKHtmlDoc;

/**
 * represent a web rendering result
 * @package IGK\System\Http
 */
class WebResponse extends RequestResponse{
    private $node;

    /**
     * enable cache on rendering
     * @var mixed
     */
    var $cache;

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
                $options = HtmlRenderer::CreateRenderOptions();
                $options->Cache = $this->cache;
                $this->node->renderAJX();
                return;
            }
        }
    }
    public function output(){
 
        ob_start();
        $cache = $this->cache; //is_object($this->node) &&  ($this->node instanceof IGKHtmlDoc);
        $this->render();
        $s = ob_get_clean();  
        $zip = igk_server()->accepts(["gzip"]);
      
        if ($cache){ 
            // + |----------------------------------------------------------------
            // + | CACHE THE DOCUMENT URI
            // + |
            list($uri, $zip) = IGKCaches::CacheUri();            
            $file = IGKCaches::page_filesystem()->getCacheFilePath($uri);
            if ($zip){
                ob_start();
                igk_zip_output($s, 0, 1);
                $s = ob_get_clean();
            }
            // save cache document for zip and no zip content
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
        } else {
            igk_set_header($this->code, $this->getStatus($this->code), $this->headers);
            echo $s;
        }
        igk_exit();
    }
}