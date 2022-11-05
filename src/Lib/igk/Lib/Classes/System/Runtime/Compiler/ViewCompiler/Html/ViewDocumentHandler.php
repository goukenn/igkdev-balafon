<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDocumentHandler.php
// @date: 20221019 09:20:46
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;

use IGK\System\Exceptions\NotImplementException;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerArgument;
use stdClass;

///<summary></summary>
/**
* use to handle document objet on compiler
* @package IGK\System\Runtime\Compiler\Html
*/
class ViewDocumentHandler implements IViewCompilerArgument{
    var $head;
    var $body;
    var $metas;
    private $m_changed;


    public function __construct(){
        $this->body = new ViewDocumentBody();
        $this->head = new ViewDocumentHead();
        $this->metas = new stdClass();
    }
    public function getMetas(){
        return $this->metas;
    }
    public function getInstruction($reset=true): ?string {
        $s = $this->m_changed ? sprintf("__set_document_attributes(%s)", [
            "title"=>null
        ]): null;
        if ($reset){
            $this->m_changed = false;
        }
        return $s;
     }
     public function renderAccessiblity(){
        
     }
    public function __call($name, $args){
        throw new NotImplementException(__CLASS__."::".$name);
    }
    

    
}