<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssComment.php
// @date: 20221202 08:50:35
namespace IGK\System\Html\Css;


///<summary></summary>
/**
* represent css comment
* @package IGK\System\Html\Css
*/
class CssComment implements ICssDefinition{
    var $value;
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getDefinition(): ?string { 
        return $this->value;
    }
}