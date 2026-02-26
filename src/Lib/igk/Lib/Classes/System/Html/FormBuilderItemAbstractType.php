<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormBuilderItemAbstractType.php
// @date: 20230626 14:55:42
namespace IGK\System\Html;
/**
* base type for custom listeral type
* @package IGK\System\Html
*/
abstract class FormBuilderItemAbstractType{

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $attribs;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    protected $id;

    /**
    * auto generate doc.
    * @param null|string $id
    */
    public function setId(?string $id){
        $this->id = $id;
    }

    /**
    * auto generate doc.
    * @param string $name
    */
    public function setName(string $name){
        $this->name = $name;
    }

    /**
    * auto generate doc.
    * @param mixed $attribs
    */
    public function setAttributes($attribs){
        $this->attribs = $attribs;
    }
    /**
     * render the custom component
     * @return null|string 
     */

    public abstract function render():?string;
}