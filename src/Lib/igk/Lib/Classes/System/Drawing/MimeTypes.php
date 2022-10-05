<?php
// @author: C.A.D. BONDJE DOUE
// @file: MimeTypes.php
// @date: 20221001 16:11:28
namespace IGK\System\Drawing;


///<summary></summary>
/**
* 
* @package IGK\System\Drawing
*/
class MimeTypes{
    public const SVG = "image/svg+xml";

    public static function Format(string $data, string $type, $code="base64"){
        return sprintf("data:%s;%s,%s", $type, $code, $data);
    }
    /**
     * format as svg
     * @param string $data 
     * @param string $type 
     * @param string $code 
     * @return string 
     */
    public static function SVGFormat(string $data, $code="base64"){
        return self::Format($data, self::SVG, $code);
    }
}