<?php

namespace IGK\System\Database\MySQL; 

use IGK\Database\DbQueryDriver as DatabaseDbQueryDriver;

/**
 * mysql query driver 
 */
class DbQueryDriver extends DatabaseDbQueryDriver {

    public static function Create($options=null){
        $o = parent::Create($options); 
        return $o;
    } 
   
}