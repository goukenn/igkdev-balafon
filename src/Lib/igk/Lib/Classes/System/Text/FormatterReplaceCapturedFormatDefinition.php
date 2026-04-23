<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterReplaceCapturedFormatDefinition.php
// @date: 20250806 20:45:12
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
abstract class FormatterReplaceCapturedFormatDefinition implements IReplaceCapturedFormatDefinition{   
    /**
     * get/Set contains chains
     * @var ?array
     */
    var $chains;
    /**
     * get/set child is dirty
     * @var ?bool
     */
    var $isDirty;
    /**
     * get/set child is splitted
     * @var ?bool
     */
    var $isSplitted;
    /**
    * Returns Has Sub Children.
    * @return bool
    */
    public function getHasSubChildren(): bool { 
        return true && $this->chains;
    } 
}