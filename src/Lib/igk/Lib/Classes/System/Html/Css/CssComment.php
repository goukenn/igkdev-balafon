<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssComment.php
// @date: 20221202 08:50:35
namespace IGK\System\Html\Css;

/**
* represent css comment
* @package IGK\System\Html\Css
*/
class CssComment implements ICssDefinition{
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
    /**
    * get string presentation.
    */
    public function __toString()
    {
        return "/* ".$this->value . "*/";
    }
}