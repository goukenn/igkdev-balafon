<?php

namespace IGK\System\Http;

use Exception;
use CssParserException;
use IGKHtmlDoc;
use ReflectionException;

/**
 * 
 * @package IGK\System\Http
 */
class DocumentRequestResponse extends RequestResponse{
    private $response; 
    /**
     * @param IGKHtmlDocument $document 
     * @return void 
     */
    public function __construct(IGKHtmlDoc $document, $code=200){
        $this->response = $document;
        $this->code = $code;
    }
    /**
     * render output document
     * @return mixed 
     * @throws Exception 
     * @throws ReflectionException 
     * @throws CssParserException 
     */
    public function render(){
        return $this->response->render();
    }
}