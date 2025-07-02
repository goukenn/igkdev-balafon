<?php
// @author: C.A.D. BONDJE DOUE
// @file: PhpDocCommentSecurityAndAuthUtility.php
// @date: 20250613 11:19:25
namespace IGK\System\Security\Helpers;

use IGK\Helper\StringUtility;
use IGK\System\IO\File\PHPDocCommentParser;
use IGK\System\IO\StringBlockReader;

/**
 * 
 * @package IGK\System\Security\Helpers
 * @author C.A.D. BONDJE DOUE
 */
class PhpDocCommentSecurityAndAuthUtility
{
    /**
     * read security and auth annotation 
     * @param string $comment 
     * @param mixed &$p 
     * @return mixed 
     */
    public static function ParseComment(string $comment, &$p)
    {
        $auth = null;
        $strict_auth = false;
        $handler = function ($m, $d, $parser) use (&$auth, &$strict_auth) {
            if ($m == 'auth') {
                if (is_string($d)) {
                    $d = StringUtility::ReadArgs(StringBlockReader::Annotation()->read($d));
                } else {
                    $d = StringUtility::ReadArgs(StringBlockReader::Annotation()->read(igk_getv($d, 0) ?? ''));
                }
                if ($auth) {
                    $d = array_merge($auth, $d);
                }
                $auth = $d;
            }
            if ($m == 'strict_auth') {
                $strict_auth = true;
            }
            if (property_exists($parser, $m)) {
                return false;
            }
            return true;
        };
        $p = PHPDocCommentParser::ParsePhpDocComment($comment, null, null, null, $handler);
        $strict_auth = (!$strict_auth) && (igk_getv($auth, 'strict') == 'true');
        unset($auth['strict']);
        $p->auth = $auth;
        $p->strict_auth = $strict_auth;
        return $p->security;
    }
}
