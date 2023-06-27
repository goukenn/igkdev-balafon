<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormBuilderItemOptions.php
// @date: 20230626 14:49:52
namespace IGK\System\Html;

use ArrayAccess;
use IGK\System\Polyfill\ArrayAccessSelfTrait;

///<summary></summary>
/**
* item properties options to pass to form builder
* @package IGK\System\Html
*/
class FormBuilderItemOptions implements ArrayAccess{
    use ArrayAccessSelfTrait;

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
    var $allow_empty;
    var $empty_value;

    var $label_attr;
    var $required;
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

    function _access_offsetGet($n){
        return $this->$n;
    }
    function _access_offsetSet($n, $v){
        $this->$n = $v;        
    }
}