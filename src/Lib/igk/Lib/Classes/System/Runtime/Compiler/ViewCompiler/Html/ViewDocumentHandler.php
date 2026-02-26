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
    * auto generate doc.
    * @var mixed
    */
    var $head;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $body;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $metas;

    /**
    * auto generate doc.
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
    * auto generate doc.
    */
    public function getMetas(){
        return $this->metas;
    }

    /**
    * auto generate doc.
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
    * auto generate doc.
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
    * auto generate doc.
    */
    public function addTempStyle(){
        $n = igk_create_node('link');
        $n["rel"] = "stylesheet";
        return $n;
    }

    /**
    * auto generate doc.
    */
    public function addTempScript(){
        $n = igk_create_node('script');
        return $n;
    }

    /**
    * auto generate doc.
    */
    public function getBody(){
        return $this->body;
    }
}