<?php


namespace IGK\System\Html;


abstract class RendererEngineBase{
    abstract function Render($node, $options = null);
}