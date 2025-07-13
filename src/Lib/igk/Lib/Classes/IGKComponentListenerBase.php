<?php
// @file: IGKComponentListenerBase.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
abstract class IGKComponentListenerBase extends IGKParamStorage implements IIGKParamHostService{
    public function __construct(){
        parent::__construct();
    }
    final function getUri($n){
        return igk_get_component_uri($this, $n);
    }
}