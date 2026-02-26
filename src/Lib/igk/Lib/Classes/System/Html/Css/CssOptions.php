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
    * auto generate doc.
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
    * auto generate doc.
    * @return ?string
    */
    public function getDefinition(): ?string {
        return $this->value;
     }
}