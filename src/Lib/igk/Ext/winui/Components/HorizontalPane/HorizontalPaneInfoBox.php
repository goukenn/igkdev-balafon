<?php
namespace IGK\Ext\WinUI\Components\HorizontalPane;
use IGK\System\Html\Dom\HtmlNode;

/**
* Horizontal pane info box.
* @package IGK\Ext\WinUI\Components\HorizontalPane
*/
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