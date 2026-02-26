<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlNodeTagExplosionTagNameAlreadyDefineException.php
// @date: 20251020 07:30:35
namespace IGK\System\Html\Exceptions;

use IGKException;
use function igk_resources_sprintf as __;

/**
* 
* @package IGK\System\Html\Exceptions
* @author C.A.D. BONDJE DOUE
*/
class HtmlNodeTagExplosionTagNameAlreadyDefineException extends IGKException{

    /**
    * .ctr
    * @param string $tagname
    */
    public function __construct(string $tagname)
    {
        parent::__construct(__('tagname already defined [%s]', $tagname));
    }
}