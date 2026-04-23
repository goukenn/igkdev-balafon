<?php
// @author: C.A.D. BONDJE DOUE
// @file: funcs.php
// @date: 20230125 16:56:44
namespace IGK\Helper\IO;

/**
 * Returns the contents of a file as a string for a given line range.
 *
 * @param string $file       Path to the file to read
 * @param int    $start_line First line index (zero-based, inclusive)
 * @param int    $end_line   Last line index (zero-based, exclusive)
 * @return string
 */
function get_file_contents_array($file, $start_line, $end_line){
    return implode("\n", array_slice(explode("\n", file_get_contents($file) ), $start_line,
        $end_line-$start_line));
}