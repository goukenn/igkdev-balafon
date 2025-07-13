<?php
// @file: IGKHtmlDoctype.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
final class HtmlDoctype extends HtmlItemBase{
    const HTML_DOCTYPE = '<!DOCTYPE html>';
    public function __construct($value){
        $this->Content=$value;
    }
    protected function _addChild($item, $index=null){
        return false;
    }
    protected function _acceptRender($options = null):bool{
        return true;
    }
    public function render($options=null){ 
        $s = trim($this->Content ?? '');
        $out="<!DOCTYPE ".$s. ">".igk_html_indent_line($options);
        return $out;
    }
}