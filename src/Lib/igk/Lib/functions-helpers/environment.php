<?php

// @author: C.A.D. BONDJE DOUE
// @filename: environment.php
// @date: 20220831 14:22:54
// @desc: environment helper functions


///<summary></summary>
///<param name="k"></param>
///<param name="v"></param>
/**
 * 
 * @param mixed $k 
 * @param mixed $v 
 */
function igk_set_env($k, $v)
{
    igk_environment()->set($k, $v);
}

///<summary></summary>
///<param name="k"></param>
///<param name="default" default="null"></param>
/**
 * 
 * @param mixed $k 
 * @param mixed $default 
 */
function igk_get_env($k, $default = null)
{
    if (empty($k) || is_object($k) || is_array($k)) {
        igk_die("illegal offset : ", __FUNCTION__);
    }
    return igk_environment()->get($k, $default);
}



///<summary></summary>
///<param name="type"></param>
///<param name="name"></param>
///<param name="callback"></param>
/**
 * 
 * @param mixed $type 
 * @param mixed $name 
 * @param mixed $callback 
 */
function igk_register_service($type, $name, $callback)
{
    $k = "sys://services/" . strtolower($type);
    $tab = igk_get_env($k, function () {
        return array();
    });
    $tab[$name] = $callback;
    igk_set_env($k, $tab);
    return $tab;
}