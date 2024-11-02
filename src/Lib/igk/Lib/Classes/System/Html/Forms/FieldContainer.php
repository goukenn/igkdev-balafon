<?php
// @author: C.A.D. BONDJE DOUE
// @file: FieldContainer.php
// @date: 20240921 10:37:49
namespace IGK\System\Html\Forms;

use IGK\System\Html\IFormFieldContainer;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms
* @author C.A.D. BONDJE DOUE
*/
class FieldContainer implements IFormFieldContainer{
    private $m_fields;
    public function __construct()
    {
        $this->m_fields = [];
    }
    /**
     * merge current fields
     * @param array $fields 
     * @return $this 
     */
    public function mergeField(array $fields, ?string $fieldset_name=null){
        if (!is_null($fieldset_name)){
            array_unshift($fields, ["type"=>"fieldset", "legend"=>$fieldset_name]);
            array_push($fields, ["type"=>"efieldset"]);
        }
        $this->m_fields = array_merge($this->m_fields, $fields);
        return $this;
    }
    /**
     * 
     * @param mixed $context 
     * @return array 
     */
    public function getFields($context=null):array{
        return $this->m_fields;
    }

    public function submit(?string $title=null){
        $submit = igk_create_node("div");
        $submit->submit()->setClass('width-a');
        $this->mergeField([
            $submit
        ], $title);
    }
}