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
* Igksystem uri action pattern info.
*/
final class IGKSystemUriActionPatternInfo extends IGKObject{

    /**
    * Properties: action, context, ctrl, keys, pattern, requestparams, uri, value.
    * @var mixed
    */
    var $action, $context, $ctrl, $keys, $pattern, $requestparams, $uri, $value;

    /**
    * auto generate doc.
    * @param mixed|object|array $tab
    * @return void
    */

    public function __construct($tab){
        foreach($tab as $k=>$v){
            $this->$k=$v;
        }
    }

    /**
    * Returns Query Params.
    */

    public function getQueryParams(){
        $t=igk_pattern_get_matches($this->pattern, $this->uri, $this->keys);
        return $t;
    }

    /**
    * Matche.
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