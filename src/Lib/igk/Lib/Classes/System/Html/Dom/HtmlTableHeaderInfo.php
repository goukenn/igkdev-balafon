<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlTableHeaderInfo.php
// @date: 20230525 18:08:30
namespace IGK\System\Html\Dom;
/**
* 
* @package IGK\System\Html\Dom
*/
class HtmlTableHeaderInfo{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $title;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $key;

    /**
    * auto generate doc.
    * @return bool
    */
    public function isEmpty():bool{
        return empty($this->key);
    }

    /**
    * auto generate doc.
    * @param HtmlNode $td
    * @param mixed $data
    * @param int $pos
    */
    public function fillEmpty(HtmlNode $td, $data, int $pos){
        $td->space();
    }

    /**
    * auto generate doc.
    * @param HtmlNode $td
    * @param mixed $v
    * @param mixed $data
    * @param int $pos
    */
    public function fillContent(HtmlNode $td, $v, $data, int $pos){
        $td->Content = $v;
    }
}