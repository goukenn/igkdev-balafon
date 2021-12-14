<?php

use IGK\System\Http\RequestHandler;
use IGK\Helper\StringUtility;
 


/**
 * represent api application base
 * @package 
 */
class IGKApiApplication extends IGKApplicationBase
{
    public function run(string $file, $render=1){
        echo "render api";
    }
}