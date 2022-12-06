<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssProperty.php
// @date: 20221202 12:40:26
namespace IGK\System\Html\Css;

use IGK\System\Html\Css\Traits\RenderDefinitionTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssProperty implements ICssDefinition{
    use RenderDefinitionTrait;
    var $name;
    var $conditions;
    var $def = [];
    var $parent;

    public function __construct(string $name, ?string $conditions = null, $parent=null)
    {
        $this->name = $name;
        $this->conditions = $conditions;
        $this->parent = $parent;
    }

    public function getDefinition(): ?string { 
        return sprintf("@%s -- %s{\n", $this->name,  $this->conditions). self::RenderDefinition($this->def). "\n}";
    }
}