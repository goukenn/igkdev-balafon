<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssMedia.php
// @date: 20221202 09:46:14
namespace IGK\System\Html\Css;
use IGK\System\Html\Css\Traits\RenderDefinitionTrait;
/**
* 
* @package IGK\System\Html\Css
*/
class CssMedia implements ICssDefinition{
    use RenderDefinitionTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $condition;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $def = [];

    /**
    * auto generate doc.
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
    * auto generate doc.
    * @return ?string
    */
    public function getDefinition():?string{
        return '@media '.$this->condition.'{'.self::RenderDefinition($this->def).'}';
    }
}