<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlTableHeaderInfo.php
// @date: 20230525 18:08:30
namespace IGK\System\Html\Dom;


///<summary></summary>
/**
* 
* @package IGK\System\Html\Dom
*/
class HtmlTableHeaderInfo{
    var $title;
    var $key;

    public function isEmpty():bool{
        return empty($this->key);
    }
    public function fillEmpty(HtmlNode $td, $data, int $pos){
        $td->space();
    }
    public function fillContent(HtmlNode $td, $v, $data, int $pos){
        $td->Content = $v;
    }
}