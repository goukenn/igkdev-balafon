<?php
// @author: C.A.D. BONDJE DOUE
// @file: UnicodeUtility.php
// @date: 20260220 12:14:23
namespace IGK\System\Text;

/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\System\Text
*/
class UnicodeUtility
{
    /**
    * auto generate doc.
    * @param int $hex
    * @return array{bytes: string[], hex: string, escape: string, utf8: string, php_u: string}
    */
    public static function UnicodeToUtf8Bytes(int $hex)
    {
        $codepoint = $hex;
        $bytes = [];
        if ($codepoint <= 0x7F) {
            $bytes[] = $codepoint;
        } elseif ($codepoint <= 0x7FF) {
            $bytes[] = 0xC0 | ($codepoint >> 6);
            $bytes[] = 0x80 | ($codepoint & 0x3F);
        } elseif ($codepoint <= 0xFFFF) {
            $bytes[] = 0xE0 | ($codepoint >> 12);
            $bytes[] = 0x80 | (($codepoint >> 6) & 0x3F);
            $bytes[] = 0x80 | ($codepoint & 0x3F);
        } elseif ($codepoint <= 0x10FFFF) {
            $bytes[] = 0xF0 | ($codepoint >> 18);
            $bytes[] = 0x80 | (($codepoint >> 12) & 0x3F);
            $bytes[] = 0x80 | (($codepoint >> 6) & 0x3F);
            $bytes[] = 0x80 | ($codepoint & 0x3F);
        } else {
            throw new \InvalidArgumentException(
                sprintf("Invalid codepoint: U+%X (max U+10FFFF)", $codepoint)
            );
        }
        $hexBytes  = array_map(fn($b) => strtoupper(sprintf('%02X', $b)), $bytes);
        $escape    = implode('', array_map(fn($h) => "\\x{$h}", $hexBytes));
        $utf8Char  = mb_chr($codepoint, 'UTF-8'); 
        return [
            'codepoint' => sprintf('U+%04X', $codepoint),
            'bytes'     => $hexBytes,           
            'hex'       => implode(' ', $hexBytes), 
            'escape'    => $escape,             
            'php_u'     => sprintf('\u{%X}', $codepoint), 
            'utf8'      => $utf8Char,           
        ];
    }
    /**
    * auto generate doc.
    * @param int $hex
    * @return mixed
    */
    public static function Char(int $hex)
    {
        return igk_getv(self::UnicodeToUtf8Bytes($hex), 'utf8');
    }
    /**
    * auto generate doc.
    * @param int $code
    * @return string
    */
    public static function RegionalChar(int $region, int $code): string
    {
        $k = self::Char($region);
        $r = self::Char($code);
        return $k . $r;
    }
    /**
    * Regional indicator.
    * @param string $letter
    * @return int
    */
    public static function RegionalIndicator(string $letter): int
    {
        return 0x1F1E6 + (ord(strtoupper($letter)) - ord('A'));
    }
    /**
    * auto generate doc.
    * @param BE
    * @return void
    */
    public static function EmojisFlag(string $countryCode){
        return self::RegionalChar(
            self::RegionalIndicator($countryCode[0]),
            self::RegionalIndicator($countryCode[1]),
        );
    }
}