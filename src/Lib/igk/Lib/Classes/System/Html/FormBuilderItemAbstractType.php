<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormBuilderItemAbstractType.php
// @date: 20230626 14:55:42
namespace IGK\System\Html;


///<summary></summary>
/**
* base type for custom listeral type
* @package IGK\System\Html
*/
abstract class FormBuilderItemAbstractType{
    protected $attribs;
    protected $name;
    protected $id;
    public function setId(?string $id){
        $this->id = $id;
    }
    public function setName(string $name){
        $this->name = $name;
    }
    public function setAttributes($attribs){
        $this->attribs = $attribs;
    }
    /**
     * render the custom component
     * @return null|string 
     */
    public abstract function render():?string;
}