<?php
// @author: C.A.D. BONDJE DOUE
// @file: TokenHelper.php
// @date: 20230304 20:05:33
namespace IGK\Helper;


///<summary></summary>
/**
* 
* @package IGK\Helpers
*/
abstract class TokenHelper{
    /**
     * get exiration times in minutes
     * @param mixed $time 
     * @return int|false 
     */
    public static function TokenDBExpirationTime($time){
       return strtotime("+$time minutes", time());
    }
}