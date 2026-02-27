<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMedia.php
// @date: 20221202 09:46:14
namespace IGK\System\Html\Css;
use IGK\System\Html\Css\Traits\RenderDefinitionTrait;

/**
* auto generate doc.
* @package IGK\System\Html\Css
*/
class CssMedia implements ICssDefinition{
    use RenderDefinitionTrait;

    /**
    * Property: condition.
    * @var mixed
    */
    var $condition;

    /**
    * Property: def.
    * @var mixed
    */
    var $def = [];

    /**
    * Property: parent.
    * @var mixed
    */
    var $parent = null;

    /**
    * .ctr
    * @param string $condition
    * @param null|mixed $parent
    */
    public function __construct(string $condition, $parent = null)
    {
        $this->condition = $condition;
        $this->parent = $parent;
    }

    /**
    * Returns Definition.
    * @return ?string
    */
    public function getDefinition():?string{
        return '@media '.$this->condition.'{'.self::RenderDefinition($this->def).'}';
    }
}