<?php
// @author: C.A.D. BONDJE DOUE
// @file: WebFileContentResponse.php
// @date: 20250125 09:09:56
namespace IGK\System\Http;


/**
* 
* @package IGK\System\Http
* @author C.A.D. BONDJE DOUE
*/
class WebFileContentResponse extends WebFileResponse{
    public function render(){
        return $this->file;
    }
}