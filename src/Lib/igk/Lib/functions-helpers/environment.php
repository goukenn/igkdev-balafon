<?php
// @author: C.A.D. BONDJE DOUE
// @filename: environment.php
// @date: 20220831 14:22:54
// @desc: environment helper functions
/**
* auto generate doc.
* @param mixed $k
* @param mixed $v
* @return mixed
*/
function igk_set_env($k, $v)
{
    igk_environment()->set($k, $v);
}
/**
* auto generate doc.
* @param mixed $k
* @param mixed $default
* @return mixed
*/
function igk_get_env($k, $default = null)
{
    if (empty($k) || is_object($k) || is_array($k)) {
        igk_die("illegal offset : ", __FUNCTION__);
    }
    return igk_environment()->get($k, $default);
}
/**
* auto generate doc.
* @param mixed $type
* @param mixed $name
* @param mixed $callback
* @return mixed
*/
function igk_register_service($type, $name, $callback)
{
    $k = IGK_SERVICE_PREFIX_PATH . strtolower($type);
    $tab = igk_get_env($k, function () {
        return array();
    });
    $tab[$name] = $callback;
    igk_set_env($k, $tab);
    return $tab;
}