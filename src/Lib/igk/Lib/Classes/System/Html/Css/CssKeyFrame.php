<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssKeyFrame.php
// @date: 20221202 10:24:25
namespace IGK\System\Html\Css;

use IGK\System\Html\Css\Traits\RenderDefinitionTrait;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssKeyFrame implements ICssDefinition{
    use RenderDefinitionTrait;
    var $name;
    var $def = [];
    var $parent;
    public function __construct(string $name, $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }
    public function getDefinition():?string{
        return '@keyframes '.$this->name.'{'.self::RenderDefinition($this->def).'}';
    }
}