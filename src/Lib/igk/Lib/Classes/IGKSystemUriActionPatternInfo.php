<?php
// @file: IGKSystemUriActionPatternInfo.php
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
final class IGKSystemUriActionPatternInfo extends IGKObject{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $action, $context, $ctrl, $keys, $pattern, $requestparams, $uri, $value;
    /**
     * 
     * @param mixed|object|array $tab 
     * @return void 
     */

    public function __construct($tab){
        foreach($tab as $k=>$v){
            $this->$k=$v;
        }
    }

    /**
    * auto generate doc.
    */

    public function getQueryParams(){
        $t=igk_pattern_get_matches($this->pattern, $this->uri, $this->keys);
        return $t;
    }

    /**
    * auto generate doc.
    * @param null|mixed $uri
    */

    public function matche($uri=null){
        $uri=$uri ?? $this->uri; 
        if($uri && preg_match($this->pattern, $uri)){
            $this->uri=$uri;
            return true;
        }
        return false;
    }
}