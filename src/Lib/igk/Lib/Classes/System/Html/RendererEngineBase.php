<?php
// @author: C.A.D. BONDJE DOUE
// @filename: RendererEngineBase.php
// @date: 20220803 13:48:56
// @desc: 



namespace IGK\System\Html;


abstract class RendererEngineBase{
    abstract function Render($node, $options = null);
}