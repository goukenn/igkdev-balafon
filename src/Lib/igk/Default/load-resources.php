<?php
// @author: C.A.D. BONDJE DOUE
// @filename: load-resources.php
// @date: 20260907 11:57:18
// @desc: resources loading 

if (!function_exists('igk_header_cache_output')) {
    /**
     * specifu cache output
     * @param mixed $second
     * @return mixed
     */
    function igk_header_cache_output($second = 3600)
    {
        $ts = gmdate("D, d M Y H:i:s", time() + $second) . " GMT";
        header("Expires: {$ts}");
        header("Pragma: cache");
        header("Cache-Control: max-age={$second}, public");
    }
}
if (!function_exists('igk_load_resources_get_mimetype')) {
    function igk_load_resources_get_mimetype(string $file): string
    {
        $rtab = ['css' => 'text/css'];
        $tab = explode('.', basename($file));
        $ext = array_pop($tab);
        if (isset($rtab[$ext])) {
            return $rtab[$ext];
        }
        $finfo = new \finfo(\FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file);
        return $mime_type;
    }
}
if (isset($_SERVER['PATH_TRANSLATED'])) {
    $path = $_SERVER['PATH_TRANSLATED'];
    if (file_exists($path)) {
        $mime_type = igk_load_resources_get_mimetype($path);  
        header('Content-Type:'. $mime_type);
        //  header('Content-Length: ' . filesize($path));
        igk_header_cache_output();
        readfile($path);
        exit;
    }
}
header('Status: 404');
exit;
