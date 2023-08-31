<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationHelper.php
// @date: 20230731 09:43:36
namespace IGK\System\Helpers;

use IGK\System\Annotations\PhpDocBlocReader;
use IGK\System\IAnnotation; 
use IGK\System\IO\StringBuilder;
use IGKException;
use ReflectionMethod;

///<summary></summary>
/**
 * 
 * @package IGK\System\Helper
 */
final class AnnotationHelper
{
    private static $sm_cacheData;

    private static function &_GetCacheData()
    {
        return self::$sm_cacheData;
    }
    private static function _ReadUsesFileHeader(string $file, &$info = null)
    {
        $tokens = token_get_all(file_get_contents($file), 0);
        $sb = new StringBuilder;
        $info = [
            'namespace' => null
        ];
        $exclude = [
            T_CLASS,
            T_INTERFACE,
            T_TRAIT,
            T_ABSTRACT,
            T_PUBLIC,
            T_FINAL
        ];
        $rns = 0;
        while (count($tokens)) {
            $v = $q = array_shift($tokens);
            // allow top namespace read on use read outside class-trait-interface or function declration 
            if (is_array($q)) {
                $v = $q[1];
                $q = $q[0];
            }
            if (in_array($q, $exclude)) {
                break;
            }
            $skip = 0;
            switch ($q) {
                case T_NAMESPACE:
                    $rns = 1;
                    break;
                case T_NAME_QUALIFIED: // 265 
                    if ($rns) {
                        $info['namespace'] = $v;
                        $rns = 0;
                    }
                    break;
                case T_COMMENT:                
                    $skip = true;
                    break;
                case 388:
                    $skip = true;
                    break;
            }
            if (!$skip)
            $sb->append($v);
        }
        return $sb.'';
    }
    /**
     * retrieve class annotations
     * @param string $class_name 
     * @return ?array
     * @throws IGKException 
     */
    public static function GetClassAnnotations(string $class_name)
    {
        if (!self::$sm_cacheData){
            self::$sm_cacheData = [];
        }
        if (key_exists($class_name, self::$sm_cacheData)){
            $p = self::$sm_cacheData[$class_name];
            return $p ? $p->annotations : null;
        }
        $v_uses = self::GetUses($class_name);
        $ref = igk_sys_reflect_class($class_name);
        $comment = $ref->getDocComment();
        if ($comment) {
            $reader = new PhpDocBlocReader;
            $p = $reader->readDoc($comment, $v_uses);
            $r_annotations = [];
            $v_loads = [];
            foreach ($p->getAnnotations() as $a) {
                if ($a instanceof IAnnotation) {
                    $cl = get_class($a);
                    if (!isset($v_loads[$cl])) {
                        $v_loads[$cl] = self::GetAnnotationInfo($cl);
                    }
                    $info = $v_loads[$cl];
                    if (in_array('class', explode('|', $info->support))) {
                        if (($info->count == 0) || ($info->multiple)) {
                            $r_annotations[] = $a;
                            $info->count++;
                        }
                    }
                }
            }
            self::$sm_cacheData[$class_name] = (object)[
                'timestamp'=>filemtime($ref->getFileName()),
                'annotations'=>$r_annotations
            ];
            return $r_annotations;
        }
        self::$sm_cacheData[$class_name] = null;
        return null;
    }
    /**
     * get uses
     * @param string $class_name 
     * @return mixed 
     * @throws IGKException 
     */
    public static function GetUses(string $class_name){
        $ref = igk_sys_reflect_class($class_name);
        $v_fn = $ref->getFileName();
        $cache_data = &self::_GetCacheData();

        if (isset($cache_data[$v_fn])) {
            $v_info = $cache_data[$v_fn];
            if (intval(filemtime($v_fn)) <= $v_info->timestamp) {
                return $v_info->annotations;
            }
        }

        $content  = self::_ReadUsesFileHeader($v_fn, $vinfo);
        $v_uses = [];
        if ($v = preg_match_all("/use\s+(?P<name>[^\s;]+)(\s+as\s+(?P<alias>[^\s+;]+))?/im", $content, $tab)) {
            for ($i = 0; $i < $v; $i++) {
                $n = $tab['name'][$i];
                $a = $tab['alias'][$i];
                $v_uses[$n] = basename(igk_uri(empty($a) ? $n : $a));
            }
        }
        return $v_uses;
    }
    /**
     * get method annotation 
     * @param ReflectionMethod $method 
     * @return array|null 
     * @throws IGKException 
     */
    public static function GetMethodAnnotations(ReflectionMethod $method)
    {
        $class = $method->getDeclaringClass()->getName();
        $v_uses = self::GetUses($class);

        $comment = $method->getDocComment();
        if ($comment) {
            $reader = new PhpDocBlocReader;
            $p = $reader->readDoc($comment, $v_uses);
            $r_annotations = [];
            $v_loads = [];
            foreach ($p->getAnnotations() as $a) {
                if ($a instanceof IAnnotation) {
                    $cl = get_class($a);
                    if (!isset($v_loads[$cl])) {
                        $v_loads[$cl] = self::GetAnnotationInfo($cl);
                    }
                    $info = $v_loads[$cl];
                    if (in_array('method', explode('|', $info->support))) {
                        if (($info->count == 0) || ($info->multiple)) {
                            $r_annotations[] = $a;
                            $info->count++;
                        }
                    }
                }
            }
            return $r_annotations;
        }
        return null;
    }
    static function GetAnnotationInfo(string $class_name)
    {
        $info = self::GetClassAnnotations($class_name);
        $pinfo = igk_getv($info, 0);

        return (object)[
            'count' => 0,
            'class' => $class_name,
            'info' => '',
            'support' => $pinfo? $pinfo->target : 'class|method',
            'multiple' => $pinfo? $pinfo->multiple : true,
        ];
    }
} 