<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMedia.php
// @date: 20221202 09:46:14
namespace IGK\System\Html\Css;

use IGK\System\Html\Css\Traits\RenderDefinitionTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssMedia implements ICssDefinition{
    use RenderDefinitionTrait;
    var $condition;
    var $def = [];
    var $parent = null;
    public function __construct(string $condition, $parent = null)
    {
        $this->condition = $condition;
        $this->parent = $parent;
    }
    public function getDefinition():?string{
        return '@media '.$this->condition.'{'.self::RenderDefinition($this->def).'}';
    }
}