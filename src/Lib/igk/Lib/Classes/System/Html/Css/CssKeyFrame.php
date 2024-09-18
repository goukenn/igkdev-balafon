<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssKeyFrame.php
// @date: 20221202 10:24:25
namespace IGK\System\Html\Css;

use Error;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\Css\Traits\RenderDefinitionTrait;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
* 
* @package IGK\System\Html\Css
*/
class CssKeyFrame implements ICssDefinition{
    use RenderDefinitionTrait;
    /**
     * name of the key frame
     * @var string
     */
    var $name;
    /**
     * key frame definition 
     * @var array
     */
    var $def = [];
    /**
     * parent key frames 
     * @var ?CssKeyFrame
     */
    var $parent;
    public function __construct(string $name, $parent = null)
    {
        $this->name = $name;
        $this->parent = $parent;
    }
    /**
     * ICssRender
     * @param ?ICssRenderOption  $option 
     * @return null|string 
     * @throws Error 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function getDefinition($option=null):?string{
        return '@keyframes '.$this->name.'{'.self::RenderDefinition($this->def, $option).'}';
    }
}