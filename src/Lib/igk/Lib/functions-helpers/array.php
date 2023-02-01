<?php

// @author: C.A.D. BONDJE DOUE
// @filename: array.php
// @date: 20230119 08:32:49
// @desc: store array her utility 

// + | --------------------------------------------------------------------
// + | 
// + |

if (!function_exists("igk_array_peek_last")) {
    /**
     * load array
     * @param mixed $tab 
     * @return mixed 
     */
    function igk_array_peek_last($tab)
    {
        if (($c = count($tab)) > 0) {
            return $tab[$c - 1];
        }
        return null;
    }
}

/**
 * append array keys
 */
function igk_array_set(& $array, $key, $value){
    $m = igk_getv($array, $key);
    if ($m){
        if (!is_array($m)){
            $m = [$m];
        }
        $m[] = $value;
    }else{
        $m = $value;
    }
    $array[$key] = $m;
}

if (!function_exists("igk_array_unique_string")) {
function igk_array_unique_string($sep, $source, $add){
    return implode(",", array_unique(array_merge(explode($sep, $source), 
        explode($sep, $add))));
}
}



///<summary></summary>
///<param name="$c"></param>
///<param name="from"></param>
///<param name="to" default="-1"></param>
/**
 * 
 * @param mixed $$c 
 * @param mixed $from 
 * @param mixed $to 
 */
function igk_array_copy($c, $from = 0, $to = -1)
{
    $tab = array();
    $t = ($to == -1) ? count($c) : $to;
    if ($t <= igk_count($c)) {
        foreach ($c as $k => $v) {
            $tab[$k] = $v;
            $t--;
            if ($t <= 0)
                break;
        }
    }
    return $tab;
}
///<summary> create a key array for value</summary>
/**
 *  create a key array for value
 */
function igk_array_createkeyarray($tab, $default = 1)
{
    return array_fill_keys($tab, $default);
}
///<summary>exclude some properties array</summary>
/**
 * exclude some properties array
 */
function igk_array_exclude($args, $property)
{
    if (is_string($property))
        $property = explode("|", $property);
    if ($args) {
        foreach ($property as $s) {
            unset($args[$s]);
        }
    }
    return $args;
}
///<summary>extract property list</summary>
///<exemple>igk_array_extract([], 'one|two')</exemple>
///<exemple>igk_array_extract([], ['one', 'two'])</exemple>
/**
 * extract property list
 * @param mixed $t source object 
 * @param string|array $property list of data to extract
 * @return ?array
 */
function igk_array_extract($t, $property): ?array
{
    if (is_string($property)) {
        $property = explode("|", $property);
    }
    if (empty($property)) {
        return null;
    }
    $tab = [];
    foreach ($property as $k) {
        $tab[$k] = igk_getv($t, $k);
    }
    return $tab;
}
///<summary></summary>
///<param name="tab"></param>
///<param name="size"></param>
///<param name="default"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $size 
 * @param mixed $default 
 */
function igk_array_fill($tab, $size = 0, $default = 0)
{
    if (($s = igk_count($tab)) < $size) {
        while ($s > 0) {
            $tab[] = $default;
            $s--;
        }
    }
    return $tab;
}
///<summary>array filter data, throw if require parameter is missing</summary>
/**
 * array filter data, throw if require parameter is missing
 */
function igk_array_filter($data, $list, $die = true)
{
    $q = [];
    foreach ($list as $k => $v) {
        if (!array_key_exists($k, $data)) {
            if ($v) {
                if ($die)
                    igk_die("require parameter not present : " . $k);
                else
                    return null;
            }
            continue;
        }
        $q[$k] = $data[$k];
    }
    return $q;
}
///<summary></summary>
///<param name="$c"></param>
/**
 * 
 * @param mixed $$c 
 */
function igk_array_first($c)
{
    if (is_array($c) && (igk_count($c) > 0)) {
        return $c[0];
    }
    return null;
}
///<summary>get if an array is assoc array</summary>
/**
 * get if an array is assoc array
 */
function igk_array_is_assoc(array $tab)
{
    foreach (array_keys($tab) as $c) {
        if (!is_numeric($c))
            return true;
    }
    return false;
}
///<summary>get if an array is indexed</summary>
/**
 * get if an array is indexed
 */
function igk_array_is_indexed($arr)
{
    return array_values($arr) === $arr;
}
///<summary>used to get array of toggled value . keys=>keys </summary>
/**
 * used to get array of toggled value . keys=>keys 
 */
function igk_array_key_value_toggle($d)
{
    $b = array();
    foreach ($d as $k => $v) {
        if (isset($b[$v]))
            continue;
        $b[$v] = $k;
    }
    return $b;
}
///<summary></summary>
///<param name="$c"></param>
/**
 * 
 * @param mixed $$c 
 */
function igk_array_last($c)
{
    if (is_array($c) && (igk_count($c) > 0)) {
        return $c[igk_count($c) - 1];
    }
    return null;
}
///<summary>Represente igk_array_log_print function</summary>
///<param name="tab" type="array"></param>
/**
 * Represente igk_array_log_print function
 * @param array $tab 
 */
function igk_array_log_print(array $tab)
{
    $str = "";
    array_map(
        function ($v, $k) use (&$str) {
            $str .= $k . ":" . igk_ob_get($v) . "\n";
        },
        $tab,
        array_keys($tab)
    );
    return $str;
}
///<summary>Create a reference assoc key in define object</summary>
///<param name="d">array of object</param>
///<param name="key">key that will be used as the association key</param>
/**
 * Create a reference assoc key from object
 * @param mixed $d array of object
 * @param mixed $key key that will be used as the association key
 */
function igk_array_object_refkey($d, $key)
{
    $b = array();
    if ($d) {
        foreach ($d as $v) {
            if (is_object($v))
                $b[$v->$key] = $v;
        }
    }
    return $b;
}
///<summary>add value to array. if key is present make an array</summary>
/**
 * add value to array. if key is present make an array
 */
function igk_array_push_keyvalue(&$tab, $k, $v, $replace = true)
{
    if (!isset($tab[$k]) || $replace) {
        $tab[$k] = $v;
    } else {
        if (is_array($tab[$k])) {
            $tab[$k][] = $v;
        } else {
            $t = array($tab[$k]);
            $t[] = $v;
            $tab[$k] = $t;
        }
    }
}
///<summary>remove empty entries from the table</summary>
/**
 * remove empty entries from the table
 */
function igk_array_remove_empty(&$tab)
{
    $ot = array();
    foreach ($tab as $v) {
        if (empty($v))
            continue;
        $ot[] = $v;
    }
    $tab = $ot;
    return $ot;
}
///<summary></summary>
///<param name="tab" ref="true"></param>
///<param name="key"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $key 
 */
function igk_array_sort_bykey(&$tab, $key)
{
    $sorter = new IGKSorter();
    $sorter->key = $key;
    usort($tab, array($sorter, "SortValue"));
}
///<summary>array utility. sort assoc array by key</summary>
/**
 * array utility. sort assoc array by key
 */
function igk_array_sortbykey(&$tab)
{
    $k = array_keys($tab);
    $o = array();
    sort($k);
    foreach ($k as $s => $t) {
        $o[$t] = $tab[$t];
    }
    $tab = $o;
    return $tab;
}
///<summary></summary>
///<param name="tab" ref="true"></param>
/**
 * 
 * @param mixed $tab 
 */
function igk_array_sortkey(&$tab)
{
    if (!is_array($tab))
        return false;
    $ckey = array_keys($tab);
    igk_usort($ckey, "igk_key_sort");
    $t = array();
    foreach ($ckey as $k) {
        $t[$k] = $tab[$k];
    }
    $tab = $t;
    return true;
}
///<summary>convert assoc-array to object presentation</summary>
/**
 * convert assoc-array to object presentation
 */
function igk_array_to_obj($c, $ns)
{
    if (($c == null) || !is_array($c)) {
        igk_assert_die(!igk_sys_env_production(), __FUNCTION__ . " Invalid argument");
        return null;
    }
    $t = array();
    $ln = strlen($ns) + 1;
    $ln -= $ns[$ln - 2] == '/' ? 1 : 0;
    foreach ($c as $k => $v) {
        if (!strstr($k, $ns))
            continue;
        $n = substr($k, $ln);
        $tt = explode("/", $n);
        if (igk_count($tt) == 1) {
            $t[$n] = $v;
        } else {
            $g = null;
            $nn = array_pop($tt);
            foreach ($tt as $m) {
                if (($g == null)) {
                    if (isset($t[$m])) {
                        $g = $t[$m];
                    } else {
                        $g = igk_createobj();
                        $t[$m] = $g;
                    }
                } else {
                    if (!isset($g->$m)) {
                        $g->$m = igk_createobj();
                        $g = $g->$m;
                    }
                }
            }
            $g->$nn = $v;
        }
    }
    return (object)$t;
}
///<summary>Used to convert array to values to assoc table of (value => value)</summary>
/**
 * Used to convert array to values to assoc table of (value => value)
 * helper: array_combine($d, $d)
 * helper: array_fill_keys
 */
function igk_array_tokeys($d, $value = true)
{
    if (!$d) {
        igk_trace();
        igk_wln_e("array key failed", $value);
    }
    if ($value) {
        return array_combine($d, $d);
    }
    $b = array();
    foreach ($d as $v) {
        $b[$v] = $value ? $v : null;
    }
    return $b;
}
///<summary></summary>
///<param name="tab"></param>
///<param name="obj"></param>
/**
 * 
 * @param mixed $tab 
 * @param mixed $obj 
 */
function igk_array_value_exist($tab, $obj)
{
    if ($tab === null) {
        igk_die(__FILE__ . ":" . __LINE__, __FUNCTION__);
    }
    foreach ($tab as $v) {
        if ($v === $obj)
            return true;
    }
    return false;
}

function igk_array_replace_key(array & $tab, $oldkey, $newkey, $value){
    $keys = array_keys($tab);
    $pos = array_search($oldkey, $keys);
    $keys[$pos] = $newkey;
    $vtabcl = array_slice($tab,0, $pos) ;
    $vtabcl[] = $value;
    $vtabcl += array_slice($tab, $pos+1);
    $vtabcl = array_combine($keys, array_values($vtabcl)); 
    $tab = $vtabcl;
}
/**
 * remove array keys and return a tab
 */
function igk_array_remove_keys(array $tab, array $keys): array{
    foreach($keys as $k){
        unset($tab[$k]);
    }
    return $tab;
}