<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlTableHeaderInfo.php
// @date: 20230525 18:08:30
namespace IGK\System\Html\Dom;

/**
* auto generate doc.
* @package IGK\System\Html\Dom
*/
class HtmlTableHeaderInfo{

    /**
    * Property: title.
    * @var mixed
    */
    var $title;

    /**
    * Property: key.
    * @var mixed
    */
    var $key;

    /**
    * Returns true if Empty.
    * @return bool
    */
    public function isEmpty():bool{
        return empty($this->key);
    }

    /**
    * Fill empty.
    * @param HtmlNode $td
    * @param mixed $data
    * @param int $pos
    */
    public function fillEmpty(HtmlNode $td, $data, int $pos){
        $td->space();
    }

    /**
    * Fill content.
    * @param HtmlNode $td
    * @param mixed $v
    * @param mixed $data
    * @param int $pos
    */
    public function fillContent(HtmlNode $td, $v, $data, int $pos){
        $td->Content = $v;
    }
}