<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldAnnotation.php
// @date: 20240103 19:08:45
namespace IGK\System\Html\Forms\Validations\Annotations;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Forms\Validations\Annotation
* @author C.A.D. BONDJE DOUE
*/
class FormFieldAnnotation extends ValidateWithAnnotation{
    var $id;

    var $placeholder;

    var $label_text;

    var $default;

    var $data;

    public function setLabel_Text(?string $value){
        $this->label_text = $value;
    }
}