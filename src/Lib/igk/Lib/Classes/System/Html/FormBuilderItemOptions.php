<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormBuilderItemOptions.php
// @date: 20230626 14:49:52
namespace IGK\System\Html;
use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;
/**
* item properties options to pass to form builder
* @package IGK\System\Html
*/
class FormBuilderItemOptions implements ArrayAccess{
    use ArrayAccessSelfTrait;

    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
     * text to litteral presentation
     * @var mixed
     */
    var $text;
    /**
     * type of formbuilder 
     * @var string|FormBuilderItemAbstractType items text|
     */
    var $type;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $allow_empty;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $empty_value;
    /**
     * label attribute 
     * @var mixed
     */
    var $label_attr;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $required;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $placeholder;
    /**
     * id to attach to input or text area
     * @var mixed
     */
    var $id;
    /**
     * attribute of this
     * @var mixed
     */
    var $attribs;
    /**
     * array of data for combobox
     * @var mixed
     */
    var $data;

    /**
    * Access offset get.
    * @param mixed $n
    */
    function _access_offsetGet($n){
        return $this->$n;
    }

    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    function _access_offsetSet($n, $v){
        $this->$n = $v;        
    }
}