<?php
// @file: IGKQueryListener.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
*/
final class DbQueryListener{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $squery;
    ///query list

    /**
    * .ctr
    */
    public function __construct(){
        $this->squery="";
    }

    /**
    * auto generate doc.
    * @param mixed $s
    */

    function sendQuery($s){
        if(preg_match("/^SELECT Count\(\*\) FROM/i", $s)){
            return null;
        }
        if(preg_match("/^SELECT \* FROM/i", $s)){
            return true;
        }
        if(preg_match("/^(set|commit|START)/i", $s)){
            $s="#".$s;
        }
        $this->squery .= $s.IGK_LF;
        if(preg_match("/^(use|select|create|insert|update) /i", $s)){
            return true;
        }
        return false;
    }
}