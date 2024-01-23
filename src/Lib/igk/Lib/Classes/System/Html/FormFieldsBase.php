<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldsBase.php
// @date: 20230929 18:05:43
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
abstract class FormFieldsBase implements IFormFields{

    public abstract function getFields(): array;

    public function getDataSource(): ?array { 
        return null;
    }

    /**
     * get block tag
     * @return null|string 
     */
    public function getTag(): ?string { 
        return IGK_FORM_FIELD_BLOCK_TAG_NAME;
    }

    public function getEngine(): ?object { 
        return null;
    }

}