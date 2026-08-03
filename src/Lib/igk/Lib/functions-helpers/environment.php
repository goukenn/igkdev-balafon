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
 * get service key definition 
 * @param string $type 
 * @return string 
 */
function igk_get_service_key(string $type):string{
    return IGK_SERVICE_PREFIX_PATH . strtolower($type);
}
/**
 * get registrated action service by service type
 * @param string $service_type service root type name
 * @return mixed
 */
function igk_get_services(string $service_type)
{
    $k = igk_get_service_key($service_type);
    return igk_get_env($k);
}
/**
* register action service 
* @param string $type
* @param string $name
* @param callable $callback
* @return mixed registrated service key 
*/
function igk_register_service(string $type,string $name,callable $callback)
{
    $k = igk_get_service_key($type);
    $tab = igk_get_env($k, function () {
        return array();
    });
    $tab[$name] = $callback;
    igk_set_env($k, $tab);
    return $tab;
}