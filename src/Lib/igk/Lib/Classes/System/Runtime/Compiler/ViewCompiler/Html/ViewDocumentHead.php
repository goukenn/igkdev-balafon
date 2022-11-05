<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDocumentHead.php
// @date: 20221019 14:19:32
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;

use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerArgument;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Html
*/
class ViewDocumentHead extends HtmlNoTagNode implements IViewCompilerArgument{
    use ViewCompilerArgumentNodeTrait;
}