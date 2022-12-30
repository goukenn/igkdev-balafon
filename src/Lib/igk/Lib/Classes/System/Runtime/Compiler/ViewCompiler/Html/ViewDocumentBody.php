<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDocumentBody.php
// @date: 20221019 14:19:37
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;

use IGK\System\Html\Dom\HtmlBodyNode;
use IGK\System\Html\Dom\HtmlNoTagNode;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerArgument;

///<summary></summary>
/**
* 
* @package IGK\System\Runtime\Compiler\Html
*/
class ViewDocumentBody extends HtmlNoTagNode implements IViewCompilerArgument{
    use ViewCompilerArgumentNodeTrait; 

    private $m_body;

    
    protected function initialize()
    {
        $this->m_body = new HtmlBodyNode;
    }
    public function getBodyBox(){
        return $this->m_body->getBodyBox();
    }
}