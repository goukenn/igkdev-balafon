<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormFieldsBase.php
// @date: 20230929 18:05:43
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
abstract class FormFieldsBase implements IFormFields{

    /**
    * auto generate doc.
    * @return array
    */
    public abstract function getFields(): array;

    /**
    * auto generate doc.
    * @return ?array
    */
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

    /**
    * auto generate doc.
    * @return ?object
    */
    public function getEngine(): ?object { 
        return null;
    }
}