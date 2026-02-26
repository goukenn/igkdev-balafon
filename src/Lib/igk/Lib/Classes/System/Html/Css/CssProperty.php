<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssProperty.php
// @date: 20221202 12:40:26
namespace IGK\System\Html\Css;
use IGK\System\Html\Css\Traits\RenderDefinitionTrait;
/**
* 
* @package IGK\System\Html\Css
*/
class CssProperty implements ICssDefinition{
    use RenderDefinitionTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $conditions;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $def = [];

    /**
    * auto generate doc.
    * @var mixed
    */
    var $parent;

    /**
    * .ctr
    * @param string $name
    * @param null|string $conditions
    * @param null|mixed $parent
    */
    public function __construct(string $name, ?string $conditions = null, $parent=null)
    {
        $this->name = $name;
        $this->conditions = $conditions;
        $this->parent = $parent;
    }

    /**
    * auto generate doc.
    * @return ?string
    */
    public function getDefinition(): ?string { 
        return sprintf("@%s -- %s{\n", $this->name,  $this->conditions). self::RenderDefinition($this->def). "\n}";
    }
}