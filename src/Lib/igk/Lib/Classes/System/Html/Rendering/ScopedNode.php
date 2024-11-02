<?php
// @author: C.A.D. BONDJE DOUE
// @file: ScopedNode.php
// @date: 20241016 13:33:21
namespace IGK\System\Html\Rendering;

use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\Rendering\Traits\ScopedNodeTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Rendering
* @author C.A.D. BONDJE DOUE
*/
class ScopedNode extends HtmlItemBase implements IHtmlRederingCallback{
    use ScopedNodeTrait;
    public function __construct(string $tagname)
    {
        parent::__construct();
        $this->tagname = $tagname;
    }
}