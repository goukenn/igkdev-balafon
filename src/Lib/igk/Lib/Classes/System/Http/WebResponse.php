<?php
 
// @author: C.A.D. BONDJE DOUE
// @filename: WebResponse.php
// @date: 20220425 15:39:28
// @desc: web response

namespace IGK\System\Http;

use IGK\System\Html\HtmlRenderer;
use IGKCaches;
use IGKEvents;
use IGKHtmlDoc;
use IHeaderResponse;

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

    /**
     * 
     * @param mixed $node 
     * @param int $code 
     * @param mixed $header 
     * @return void 
     */
    public function __construct($node, int $code=200, $header=null){
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
                $options->AJX = igk_is_ajx_demand();
                $this->node->renderAJX($options); 
                // raise ajx end reponse in order to add extra data to svg list 
                if ($options->AJX){ 
                    igk_hook( IGKEvents::HOOK_AJX_END_RESPONSE, [$this]);
                }
            }
        }
    }
    public function output(){        
        $cache = $this->cache;
        // + | priority to document cache setting
        if ($cache && is_object($this->node) &&  ($this->node instanceof IGKHtmlDoc)){
            $cache = !$this->node->noCache; 
        } 
        $this->_setHeader();
        
        ob_start();   
        $this->render();
        $s = ob_get_clean(); 
        /// TODO: disable gzip    
    
        $v_zip = $zip = 0;//igk_server()->accepts(["gzip"]);
      // igk_wln_e("response ....");
        if ($cache){ 
            // + |----------------------------------------------------------------
            // + | CACHE THE DOCUMENT URI
            // + |
            list($uri, $zip) = IGKCaches::CacheUri();                      
            $file = IGKCaches::page_filesystem()->getCacheFilePath($uri);  
            $zip = $zip && $v_zip;
            if ($zip){
                if (ob_get_level()){
                    ob_end_clean();
                } 
                ob_start();
                igk_zip_output($s, 0, 1);
                $s = ob_get_clean();
            }            
            // + |----------------------------------------------------------------
            // + | cache document for zip and no zip content
            // + |
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
            echo $s;
        }
        igk_exit();
    }
}