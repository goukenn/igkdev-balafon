<?php
// @author: C.A.D. BONDJE DOUE
// @file: ViewDocumentHandler.php
// @date: 20221019 09:20:46
namespace IGK\System\Runtime\Compiler\ViewCompiler\Html;
use IGK\System\Exceptions\NotImplementException;
use IGK\System\Runtime\Compiler\ViewCompiler\IViewCompilerArgument;
use stdClass;
/**
* use to handle document objet on compiler
* @package IGK\System\Runtime\Compiler\Html
*/
class ViewDocumentHandler implements IViewCompilerArgument{
    /**
    * Property: head.
    * @var mixed
    */
    var $head;
    /**
    * Property: body.
    * @var mixed
    */
    var $body;
    /**
    * Property: metas.
    * @var mixed
    */
    var $metas;
    /**
    * Property: changed.
    * @var mixed
    */
    private $m_changed;
    /**
    * .ctr
    */
    public function __construct(){
        $this->body = new ViewDocumentBody();
        $this->head = new ViewDocumentHead();
        $this->metas = new stdClass();
    }
    /**
    * Returns Metas.
    */
    public function getMetas(){
        return $this->metas;
    }
    /**
    * Returns Instruction.
    * @param mixed $reset
    * @return ?string
    */
    public function getInstruction($reset=true): ?string {
        $s = $this->m_changed ? sprintf("__set_document_attributes(%s)", [
            "title"=>null
        ]): null;
        if ($reset){
            $this->m_changed = false;
        }
        return $s;
     }
    /**
    * Renders Accessiblity.
    */
    public function renderAccessiblity(){
     }
    /**
    * Triggered when calling an inaccessible or undefined method on an object.
    * @param mixed $name
    * @param mixed $args
    */
    public function __call($name, $args){
        throw new NotImplementException(__CLASS__."::".$name);
    }
    /**
    * Adds Temp Style.
    */
    public function addTempStyle(){
        $n = igk_create_node('link');
        $n["rel"] = "stylesheet";
        return $n;
    }
    /**
    * Adds Temp Script.
    */
    public function addTempScript(){
        $n = igk_create_node('script');
        return $n;
    }
    /**
    * Returns Body.
    */
    public function getBody(){
        return $this->body;
    }
}