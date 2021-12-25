<?php

namespace IGK\System\Http;

/**
 * use to response with file
 * @package IGK\System\Http
 */
class WebFileResponse extends RequestResponse{
    /**
     * file to render
     * @var mixed
     */
    var $file;
    /**
     * file content zip content
     * @var mixed
     */
    var $zip;

    public function __construct($file)
    {
        $this->file = $file;
    }
    public function render()
    {
        ob_start();
        include($this->file);
        $s= ob_get_clean();
        return $s;
    }
    public function output()
    {
        // +| check for header mimetype according to file extension        
        $s = $this->render();
        $this->headers[] = "Content-Type: text/html";
        $this->headers[] = "Content-Length: ".strlen($s);
        if ($this->zip){
            $this->headers[] = "Content-Encoding:  deflate";
        } 
        igk_set_header($this->code, $this->getStatus($this->code), $this->headers); 
        echo $s;
        igk_exit();
    }
}