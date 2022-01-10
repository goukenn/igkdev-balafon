<?php

namespace IGK\System\Database; 

use IGK\Database\DbQueryDriver as DatabaseDbQueryDriver;

/**
 * mysql query driver 
 */
class DbQueryDriver extends DatabaseDbQueryDriver {

    public static function Create($options=null){
        return DatabaseDbQueryDriver::Create($options);
    }   
}