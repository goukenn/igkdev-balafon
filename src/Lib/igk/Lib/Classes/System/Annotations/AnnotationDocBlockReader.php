<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationDocBlockReader.php
// @date: 20230731 12:51:07
namespace IGK\System\Annotations;

use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use IGK\System\IAnnotation;
use IGK\System\IO\File\Php\PhpDocBlockBase;
use IGK\System\IO\File\Php\Traits\PHPDocCommentParseTrait;

///<summary></summary>
/**
 * 
 * @package IGK\System\Annotations
 */
class AnnotationDocBlockReader extends PhpDocBlockBase
{
    use PHPDocCommentParseTrait;


    private static $sm_uses;
    private static $sm_alias;
    var $summary;
    var $api;
    var $params;
    var $package;
    var $var;
    private $m_annotations = [];

    public static function Uses(?array $cm)
    {
        if (is_null($cm)) {
            self::$sm_alias = self::$sm_uses = null;
        } else {
            self::$sm_uses = $cm;
            self::$sm_alias = array_flip($cm);
        }
    }
    /**
     * get annocation object
     * @return array 
     */
    public function getAnnotations()
    {
        return $this->m_annotations;
    }
    private static function _TreatArgs($args)
    {
        $content = trim($args, ' ()');
        return StringUtility::ReadArgs($content);
    }
    public function __call($name, $arguments)
    {
        $cl = null;

        if (property_exists($this, $name)) {
            $tcontent = self::_TreatArgs($arguments[0]);
            $this->$name = $tcontent ? igk_getv($tcontent, 0) : true;
            return $this;
        }
        $sp = strpos($name, '\\') === false;
        $alias = $sp ? $name : basename(igk_getv(explode("\\", $name), 0));
        if (isset(self::$sm_alias[$alias])) {
            $cl = self::$sm_alias[$alias];
            if (!$sp) {
                $cl = $cl . substr($name, strlen($alias));
            }
        } else if (isset(self::$sm_uses[$name]) || class_exists($name, false)) {
            $cl = $name;
        }
        if ($cl) {
            //read args 
            $tcontent = self::_TreatArgs($arguments[0]);
            if ($cl = self::GetExistingClass($cl)) { 
                $ocl = Activator::CreateNewInstance($cl, $tcontent);
                if ($ocl instanceof IAnnotation)
                    $ocl->setParams($tcontent);
                $this->m_annotations[] = $ocl;
            }
        }
    }
    public static function GetExistingClass(string $class_name): ?string{
        foreach(['','Annotation'] as $suffix){
            if (class_exists($cl = $class_name.$suffix)){
                return $cl;
            }
        }
        return null;
    }
}
