<?php
// @author: C.A.D. BONDJE DOUE
// @file: LinePrefixMapper.php
// @date: 20221021 21:15:05
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
class LinePrefixMapper{
    /**
    * auto generate doc.
    * @var string
    */
    var $prefix;
    /**
    * auto generate doc.
    * @var string
    */
    var $suffix;
    /**
     * map source string
     * @param string $source 
     * @return string 
     */
    public function map(string $source){
        $src = implode("\n", array_map(function($a){
            return $this->prefix.trim($a).$this->suffix; 
        }, explode("\n", $source)));
        return $src;
    }
}