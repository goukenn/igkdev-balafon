<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexCaptureInfo.php
// @date: 20241106 15:57:37
namespace IGK\System\Text;
use Closure;
use IGK\Helper\Activator;
use IGK\Helper\Trait\ActivatorPrivateInitProperty;
use IGKException;
use IGKObject;
/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
/**
* auto generate doc.
* @package IGK\System\Text
*/
class RegexCaptureInfo extends IGKObject implements IRegexCaptureInfo
{
    use ActivatorPrivateInitProperty;
    /**
    * Property: pos.
    * @var mixed
    */
    private $pos;
    /**
    * Property: to.
    * @var mixed
    */
    private $to;
    /**
    * Property: value.
    * @var mixed
    */
    private $value;
    /**
    * Property: childs.
    * @var mixed
    */
    var $childs;
    /**
    * Property: data.
    * @var mixed
    */
    var $data;
    /**
    * Property: dynamic.
    * @var mixed
    */
    var $_dynamic;
    /**
    * Getis root.
    * @return bool
    */
    public function getisRoot(): bool { return true;}
    /**
    * Getis root captured.
    * @return bool
    */
    public function getisRootCaptured(): bool { return true;}
    /**
    * Returns Pos.
    */
    public function getPos(){
        return $this->pos;
    }
    /**
    * Returns To.
    */
    public function getTo(){
        return $this->to;
    }
    /**
    * Returns Value.
    */
    public function getValue(){
        return $this->value;
    }
    /**
    * .destructor
    * @param mixed $n
    */
    public function __get($n){
        if ($n === RegexTreatCapture::MARK_KEY){
            if ($this->_dynamic){
                return igk_getv($this->_dynamic, $n);
            }
            return 0;
        }  
        return parent::__get($n);
    }
    /**
    * destructor
    * @param mixed $n
    * @param mixed $v
    */
    public function __set($n, $v){
        if ($n === RegexTreatCapture::MARK_KEY){
            if (!$this->_dynamic){
                $this->_dynamic = igk_createobj();
            }
            $this->_dynamic->{$n} = $v;
            return;
        }
        parent::__set($n, $v);
    }
    /**
    * auto generate doc.
    * @param array $def
    * @return mixed
    */
    public static function CreateFrom(array $def)
    {
        $inf = Activator::CreateNewInstance(static::class, $def);
        Activator::InitPrivatePropety(self::_InitializePrivatePropertiesCallback(), $inf, $def);
        return $inf;
    } 
}