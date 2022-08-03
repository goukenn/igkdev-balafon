<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DocumentRequestResponse.php
// @date: 20220803 13:48:55
// @desc: 


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