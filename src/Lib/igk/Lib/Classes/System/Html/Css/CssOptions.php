<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssOptions.php
// @date: 20221202 08:57:43
namespace IGK\System\Html\Css;
/**
* 
* @package IGK\System\Html\Css
*/
class CssOptions implements ICssDefinition{

    /**
    * Property: value.
    * @var mixed
    */
    var $value;

    /**
    * .ctr
    * @param string $value
    */
    public function __construct(string $value)
    {
        $this->value = $value;
    }

    /**
    * Returns Definition.
    * @return ?string
    */
    public function getDefinition(): ?string {
        return $this->value;
     }
}