<?php
namespace IGK\Ext\WinUI\Components\HorizontalPane;

use IGK\System\Html\Dom\HtmlNode;

class HorizontalPaneInfoBox extends HtmlNode{
    /**
     * Indicate whether the info box is visible.
     *
     * @return bool Always returns false.
     */
    public function getIsVisible()
    {
        return false;
    }
}