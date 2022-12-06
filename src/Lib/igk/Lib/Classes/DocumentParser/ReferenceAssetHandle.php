<?php
// @author: C.A.D. BONDJE DOUE
// @file: ReferenceAssetHandle.php
// @date: 20221129 10:44:11
namespace IGK\DocumentParser;

use IGK\Controllers\BaseController;
use IGK\System\Html\IHtmlGetValue;
use IGKResourceUriResolver;

///<summary></summary>
/**
* 
* @package IGK\DocumentParser
*/
class ReferenceAssetHandle implements IHtmlGetValue
{
    var $controller;
    var $src;
    public function __construct(string $src, BaseController $controller)
    {
        $this->controller = $controller;
        $this->src = $src;
    }
    public function __toString()
    {
        return $this->getValue();
    }
    public function getValue($options = null)
    {
        return '/'.igk_str_rm_start(IGKResourceUriResolver::getInstance()->resolveOnly($this->controller->getAssetsDir($this->src)), '../');   
    }
}