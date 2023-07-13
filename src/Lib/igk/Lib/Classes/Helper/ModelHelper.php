<?php
// @author: C.A.D. BONDJE DOUE
// @file: ModelHelper.php
// @date: 20230704 14:35:57
namespace IGK\Helper;


///<summary></summary>
/**
* 
* @package IGK\Helper
*/
class ModelHelper{
    static function MapToArray(){
        return function($a){
            return $a->to_array();
        };
    }
}