<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RendererEngineBase.php
// @date: 20220803 13:48:56
// @desc: 
namespace IGK\System\Html;

/**
* Renderer engine base.
* @package IGK\System\Html
*/
abstract class RendererEngineBase{
    /**
    * Renders.
    * @param mixed $node
    * @param null|mixed $options
    */
    abstract function Render($node, $options = null);
}