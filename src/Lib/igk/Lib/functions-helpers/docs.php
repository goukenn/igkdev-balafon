<?php

// @author: C.A.D. BONDJE DOUE
// @filename: Untitled-1
// @date: 20240918 07:51:14
// @desc: document helper 


if (!function_exists('igk_doc_interface')){
    function igk_doc_interface($obj){
        $r = is_array($obj) ? $obj : array_keys((array)$obj);
        sort($r);
        $sb = [];
        foreach($r as $k){
            $sb[] = " * @property mixed $k";
        }
        return implode("\n", $sb);
    }
}

