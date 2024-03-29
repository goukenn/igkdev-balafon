<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlInitNodeInfo.php
// @date: 20220803 13:48:55
// @desc: 


namespace IGK\System\Html;

use IGK\Helper\SysUtils;
use IGKException;
use IGKObject;

class HtmlInitNodeInfo extends IGKObject{
    /**
     * 
     * @var char char that identified the type
     */
    var $type;
    /**
     * 
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