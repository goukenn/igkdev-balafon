<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssImportRule.php
// @date: 20260918 18:03:57
namespace IGK\System\Html\Css;


/**
* 
* @package IGK\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssImportRule implements ICssDefinition{
    var $type = 'import';
    var $value = null;
    public function getDefinition(): ?string{
        if ($this->value)
            return sprintf('@%s \'%s\';', $this->type, $this->value );
        return null;
    }

}