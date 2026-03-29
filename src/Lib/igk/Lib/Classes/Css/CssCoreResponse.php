<?php
// @author: C.A.D. BONDJE DOUE
// @filename: CssCoreResponse.php
// @date: 20220803 13:48:58
// @desc: 
namespace IGK\Css;
use IGK\System\Http\WebResponse;
/**
* Css core response.
* @package IGK\Css
*/
class CssCoreResponse extends WebResponse{
    /**
    * Property: file.
    * @var mixed
    */
    var $file;
    /**
    * Cache: no cache.
    * @var mixed
    */
    var $no_cache;
    /**
     * Constructor.
     *
     * @param mixed $content The CSS content for the response.
     */
    public function __construct($content)
    {
        parent::__construct($content, 200, [
            "Content-Type: text/css"
        ]);
    } 
}