<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssOptions.php
// @date: 20221202 08:57:43
namespace IGK\System\Html\Css;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssOptions implements ICssDefinition{
    var $value;
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getDefinition(): ?string {
        return $this->value;
     }
}