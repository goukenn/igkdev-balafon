<?php
// @file: IGKComponentListenerBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* Igkcomponent listener base.
*/
abstract class IGKComponentListenerBase extends IGKParamStorage implements IParamHostService{
    /**
    * .ctr
    */
    public function __construct(){
        parent::__construct();
    }
    final
    /**
    * Returns Uri.
    * @param mixed $n
    */
    function getUri($n){
        return igk_get_component_uri($this, $n);
    }
}