<?php
// @author: C.A.D. BONDJE DOUE
// @filename: MimeType.php
// @date: 20220803 13:48:55
// @desc:
namespace IGK\System\IO;

/**
* Mime type.
* @package IGK\System\IO
*/
class MimeType{
    /**
     * Returns the MIME type corresponding to the given file extension.
     *
     * @param string $ext The file extension (e.g. "png", "html").
     * @return string
     */
    public static function FromExtension($ext){
        $mime = igk_getv(igk_header_mime(),$ext, "text/plain");
        return $mime;
    }
    /**
     * Returns the MIME type for a given type identifier, falling back to an extension.
     *
     * @param string $type The type identifier to look up.
     * @param string $ext  The fallback extension value.
     * @return string
     */
    public static function FromType($type, $ext){
        static $define ;
        if ($define===null){
            $define = [];
        }
        return igk_getv($define, $type, $ext);
    }
}
