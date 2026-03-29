<?php
// @author: C.A.D. BONDJE DOUE
// @file: AttributeEncoder.php
// @date: 20230316 09:58:40
namespace IGK\System\Html\Encoding;
/**
* auto generate doc.
* @package IGK\System\Html\Encoding
*/
class AttributeEncoder{
    /**
    * Collection of char list.
    * @var mixed
    */
    var $char_list = [
        "\""=>"&quot;"
    ];
    /**
     * helper : encode attribute in system requirement 
     * @param string $value 
     * @return string 
     */
    public function Encode(string $value):string{
        $s = $value;
        foreach($this->char_list as $k=>$v){
            $s = str_replace($k, $v, $s);
        }
        return $s;
    }
    /**
     * decode char string
     * @param string $value 
     * @return string 
     */
    public function decode(string $value):string{
        $c = array_flip($this->char_list);
        $s = $value;
        foreach($c as $k=>$v){
            $s = str_replace($k, $v, $s);
        }
        return $s;
    }
}