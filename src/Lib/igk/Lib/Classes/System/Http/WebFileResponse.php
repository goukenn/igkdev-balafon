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

    /**
     * define the variable charset
     * @var mixed
     */
    var $charset;

    public function __construct($file)
    {
        $this->file = $file;
    }
    public function render()
    {
        ob_start();
        readfile($this->file);
        $s= ob_get_clean();
        return $s;
    }
    public function output()
    {
        // +| check for header mimetype according to file extension        
        $s = $this->render();
        $this->headers[] = "Content-Type: text/html";
        $index = count($this->headers)-1;
        $this->headers[] = "Content-Length: ".strlen($s);
        if ($this->zip){
            $this->headers[] = "Content-Encoding: deflate";
        } 
        if ($type = igk_io_path_ext($this->file)){
            $mime = igk_header_mime();
            $charset = $this->charset;
            if ($charset)
                $charset = ";" . $charset;
            $data = igk_getv($mime, $type, IGK_CT_PLAIN_TEXT) . $charset;
            $this->headers[$index] = "Content-Type: ".$data;
        }
        igk_set_header($this->code, $this->getStatus($this->code), $this->headers); 
        echo $s;
        igk_exit();
    }
}