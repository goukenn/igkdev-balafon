<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetectorUtils.php
// @date: 20240913 09:47:11
namespace IGK\System\Html\Css;

use Exception;
use IGK\System\Console\Logger;
use IGK\System\Text\RegexMatcherContainer;

/**
 * auto generate doc.
 * @package IGK\System\Html\Css
 * @author C.A.D. BONDJE DOUE
 */
abstract class CssClassNameDetectorUtils
{
    /**
     * auto generate doc.
     * @param CssClassNameDetector $detector
     * @param string $filename
     * @param mixed & $references
     * @return mixed|void
     */
    public static function DetectFromFile(CssClassNameDetector $detector, string $filename, &$references = null)
    {
        $ext = igk_io_path_ext($filename);
        $p = ucfirst(strtolower($ext));
        $src = file_get_contents($filename);
        if (method_exists(static::class, $fc =  "DetectFrom" . $p . "Source")) {
            return call_user_func_array([static::class, $fc], [$detector, $src, &$references]);
        }
        Logger::warn('missing detected from ' . $p);
    }
    /**
     * detected from references 
     * @param CssClassNameDetector $detector 
     * @param string $content 
     * @param mixed &$references 
     * @return array|null 
     */
    public static function DetectFromContent(CssClassNameDetector $detector, string $content, & $references)
    {
        return self::DetectFromPhpSource($detector, $content, $references);
    }

    const DETECT_METHOD_FORMAT = 'DetectFrom%sSource';

    public static function DetectFromBviewSource() {}


    /**
     * auto generate doc.
     * @param CssClassNameDetector $detector
     * @param string $source
     * @param mixed &$references
     * @param mixed $context
     * @return array|void
     */
    public static function DetectFromPhpSource(CssClassNameDetector $detector, string $source, &$references = null, $context = null)
    {
        $p = $detector->getDetector('php');
        if ($data = $p->resolve($source)) {
            return $detector->loadReferences(array_keys($data), $references);
        }  
    }
    /**
     * Detect from phtml source.
     * @param CssClassNameDetector $detector
     * @param string $source
     * @param null|mixed & $references
     */
    public static function DetectFromPHtmlSource(CssClassNameDetector $detector, string $source, &$references = null)
    {
        return self::DetectFromPhpSource($detector, $source, $references, 'phtml');
    }
    /**
     * auto generate doc.
     * @param mixed $detector
     * @param mixed $source
     * @param mixed &$references
     * @return mixed
     */
    public static function DetectFromHtmlSource($detector, $source, &$references = null)
    {
        $container = new RegexMatcherContainer;
        $container->begin("<!--", "-->", "comment");
        $container->begin("<(script|style)", "<\\/\\1\\s*>", "ignore-tag");
        $container->begin("<(?:\\w+)", ">", "tag");
        $pos = 0;
        $src = $source;
        $match = [];
        while ($g = $container->detect($src, $pos)) {
            $g = $container->end($g, $src, $pos);
            switch ($g->tokenID) {
                case 'tag':
                    $def = (object)['ref' => null];
                    $con = new RegexMatcherContainer;
                    $con->begin("\bclass(Name)?\b\s*", "=\s*", 'class');
                    $con->begin('("|\')', "\\1", "value");
                    $part = $con->extract($g->value, function ($g) use ($def) {
                        if ($def->ref) {
                            if ($g->tokenID == 'value') {
                                $def->ref = null;
                                $g->value = preg_replace("/\s+/", " ", $g->value);
                                return true;
                            }
                        } else {
                            if ($g->tokenID == 'class') {
                                $def->ref = true;
                            }
                        }
                        return false;
                    });
                    if ($part) {
                        $match = array_merge($match, $part);
                    }
                    break;
            }
        }
        return $detector->resolv(implode(' ', $match), $references);
    }

    /**
     * 
     * @param array $dirs 
     * @param CssClassNameDetector $detector 
     * @param string $file_extension 
     * @param mixed &$references 
     * @return array 
     */
    public static function GetResolvedReferences(array $dirs, CssClassNameDetector $detector, string $file_extension = 'phtml|html|bview', &$references = null)
    {
        $resolved_def = [];
        $references = [];
        while (count($dirs) > 0) {
            if ($c = array_shift($dirs)) {
                foreach (igk_io_getfiles($c, "/\.(" . $file_extension . ")$/", true) as $f) {
                    if ($r = CssClassNameDetectorUtils::DetectFromFile($detector, $f, $references)) {
                        $resolved_def = array_merge($r, $resolved_def);
                    }
                }
            }
        }
        return $resolved_def;
    }
}
