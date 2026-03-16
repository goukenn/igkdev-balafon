<?php
// @author: C.A.D. BONDJE DOUE
// @file: AnnotationDocBlockReader.php
// @date: 20230731 12:51:07
namespace IGK\System\Annotations;

use Exception;
use IGK\Helper\Activator;
use IGK\Helper\StringUtility;
use IGK\System\IAnnotation;
use IGK\System\IO\File\Php\PhpDocBlockBase;
use IGK\System\IO\File\Php\Traits\PHPDocCommentParseTrait;
use IGK\Constants;


/**
 * auto generate doc.
 * @package IGK\System\Annotations
 */
class AnnotationDocBlockReader extends PhpDocBlockBase
{
    use PHPDocCommentParseTrait;

    /**
     * Constant: before create instance method.
     * @var mixed
     */
    const BEFORE_CREATE_INSTANCE_METHOD = 'BeforeCreateInstance';

    /**
     * Property: uses.
     * @var mixed
     */
    private static $sm_uses;

    /**
     * Property: alias.
     * @var mixed
     */
    private static $sm_alias;

    /**
     * Property: summary.
     * @var mixed
     */
    var $summary;

    /**
     * Property: api.
     * @var mixed
     */
    var $api;

    /**
     * list of params 
     * @var mixed
     */
    var $params;

    /**
     * to view parameter 
     * @var mixed
     */
    var $param;
    /**
     * Property: package.
     * @var mixed
     */
    var $package;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $author;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $category;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $licence;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $copyright;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $example;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $filesource;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $final;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $global;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $ignore;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $internal;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $license;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $link;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $property_read;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $property_write;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $see;
    /**
     * source from the package 
     * @var ?string 
     */
    var $source;
    /**
     * subpackage 
     * @var mixed
     */
    var $subpackage;
    /**
     * to do 
     * @var mixed
     */
    var $todo;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $uses;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $used_by;

    /**
    * auto generate doc.
    * @var mixed
    * @return
    */
    var $version;




    /**
     * throw errors 
     * @var mixed
     */
    var $throws;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $deprecated;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $since;

    /**
     * Property: var.
     * @var mixed
     */
    var $var;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $property;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $method;

    /**
     * auto generate doc.
     * @var mixed
     */
    var $return;
    /**
     * annotation in uses
     * @var array
     */
    private $m_annotations = [];

    /**
     * auto generate doc.
     * @var mixed
     */
    private $m_extraProperties;

    /**
     * Property: filter.
     * @var mixed
     */
    private $m_filter;

    /**
     * Property: reader.
     * @var mixed
     */
    private $m_reader;

    /**
     * Uses.
     * @param null|array $cm
     */
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

    /**
     * auto generate doc.
     * @param mixed $args
     * @return
     */
    private static function _TreatArgs($args)
    {
        $content = trim($args, ' ()');
        return StringUtility::ReadArgs($content);
    }
    /**
     * resolve class type on loading
     * @param string $name 
     * @return ?string 
     * @throws Exception 
     */

    static function ResolveClassType(string $name)
    {
        $cl = null;
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
        return $cl;
    }

    /**
     * Triggered when calling an inaccessible or undefined method on an object.
     * @param mixed $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        $cl = null;
        $filter = $this->m_filter;
        if (property_exists($this, $name)) {
            $tcontent = self::_TreatArgs($arguments[0]);
            $s = $tcontent ? igk_getv($tcontent, 0) : true;
            $g = $this->$name;
            if (isset($g) && self::$sm_loading){     
                if ($this->_supportMutiple($name)){
                    if (!is_array($g)){
                        $g = [$g];
                    }
                    $g[] = $s;
                    $s = $g;
                }
            }
            $this->$name = $s;
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
            if (($cl = self::GetExistingClass($cl)) && (!$filter || in_array($cl, $filter))) {
                if (method_exists($cl, $fc = self::BEFORE_CREATE_INSTANCE_METHOD)) {
                    call_user_func_array([$cl, $fc], [$this, &$tcontent]);
                }
                try {
                    $ocl = Activator::CreateNewInstance($cl, $tcontent);
                    if ($ocl instanceof IAnnotation)
                        $ocl->setParams($tcontent);
                    if ($sp) {
                        $this->m_annotations[$name] = $ocl;
                    } else {
                        $this->m_annotations[] = $ocl;
                    }
                } catch (\TypeError $ex) {
                    $this->m_extraProperties[$name] = implode(' ', $arguments);
                }
            }
        }
    }

    /**
     * auto generate doc.
     */
    public function getExtraProperties()
    {
        return  $this->m_extraProperties;
    }
    /**
     * get existing class of block reader
     * @param string $class_name 
     * @return null|string 
     */
    public static function GetExistingClass(string $class_name): ?string
    {
        foreach (['', Constants::ANNOTATION_SUFFIX] as $suffix) {
            if (class_exists($cl = $class_name . $suffix)) {
                return $cl;
            }
        }
        return null;
    }
}
