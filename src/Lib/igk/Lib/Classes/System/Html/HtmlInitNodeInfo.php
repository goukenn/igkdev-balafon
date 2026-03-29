<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlInitNodeInfo.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\Html;
use IGK\Helper\SysUtils;
use IGKException;
use IGKObject;
/**
* Html init node info.
* @package IGK\System\Html
*/
class HtmlInitNodeInfo extends IGKObject{
    /**
    * auto generate doc.
    * @var char char that identified the type
    */
    var $type;
    /**
    * auto generate doc.
    * @var string
    */
    var $name;
    /**
     * use array to initialize info
     * @param array $tag 
     * @return mixed 
     * @throws IGKException 
     */
    public static function Create(array $tag){
        $n = new static();
        SysUtils::InitClassVars($n, $tag); 
        return $n;
    }
}