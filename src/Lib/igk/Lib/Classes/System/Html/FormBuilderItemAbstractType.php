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
    * Property: attribs.
    * @var mixed
    */
    protected $attribs;
    /**
    * Name of name.
    * @var mixed
    */
    protected $name;
    /**
    * Identifier: id.
    * @var mixed
    */
    protected $id;
    /**
    * Sets Id.
    * @param null|string $id
    */
    public function setId(?string $id){
        $this->id = $id;
    }
    /**
    * Sets Name.
    * @param string $name
    */
    public function setName(string $name){
        $this->name = $name;
    }
    /**
    * Sets Attributes.
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