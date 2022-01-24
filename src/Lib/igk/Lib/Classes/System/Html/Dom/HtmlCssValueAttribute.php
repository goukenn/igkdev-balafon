<?php
namespace IGK\System\Html\Dom;

use IGK\Css\CssThemeCompiler;
use IGK\System\Html\IHtmlGetValue;

class HtmlCssValueAttribute implements IHtmlGetValue{
    var $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
    public function getValue($options = null) { 
        if (CssThemeCompiler::CanCompile($this->value)){
            $systheme = $options->Document->getSysTheme();
            $compiler = new CssThemeCompiler($systheme->getDef()->getCl(), false);
            return $compiler->treatValue($this->value, $options->Document->getTheme(), $options->Document->getSysTheme());
        }
        return $this->value;

    }

}