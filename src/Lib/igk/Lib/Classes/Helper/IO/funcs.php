<?php
// @author: C.A.D. BONDJE DOUE
// @file: funcs.php
// @date: 20230125 16:56:44
namespace IGK\Helper\IO;


function get_file_contents_array($file, $start_line, $end_line){
    return implode("\n", array_slice(explode("\n", file_get_contents($file) ), $start_line,
        $end_line-$start_line));
}