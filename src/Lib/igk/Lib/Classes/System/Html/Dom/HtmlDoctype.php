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
/**
* Html doctype.
* @package IGK\System\Html\Dom
*/
final class HtmlDoctype extends HtmlItemBase{
    /**
    * Constant: html doctype.
    * @var mixed
    */
    const HTML_DOCTYPE = '<!DOCTYPE html>';
    /**
    * .ctr
    * @param mixed $value
    */
    public function __construct($value){
        $this->Content=$value;
    }
    /**
    * Add child.
    * @param mixed $item
    * @param null|mixed $index
    */
    protected function _addChild($item, $index=null){
        return false;
    }
    /**
    * Accept render.
    * @param null|mixed $options
    * @return bool
    */
    protected function _acceptRender($options = null):bool{
        return true;
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options=null){ 
        $s = trim($this->Content ?? '');
        $out="<!DOCTYPE ".$s. ">".igk_html_indent_line($options);
        return $out;
    }
}