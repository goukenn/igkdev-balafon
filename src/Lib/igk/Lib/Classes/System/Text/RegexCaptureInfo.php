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
class RegexCaptureInfo extends IGKObject implements IRegexCaptureInfo
{
    use ActivatorPrivateInitProperty;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $pos;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $to;

    /**
    * auto generate doc.
    * @var mixed
    */
    private $value;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $childs;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $data;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $_dynamic;

    /**
    * auto generate doc.
    * @return bool
    */
    public function getisRoot(): bool { return true;}

    /**
    * auto generate doc.
    * @return bool
    */
    public function getisRootCaptured(): bool { return true;}

    /**
    * auto generate doc.
    */
    public function getPos(){
        return $this->pos;
    }

    /**
    * auto generate doc.
    */
    public function getTo(){
        return $this->to;
    }

    /**
    * auto generate doc.
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
     * 
     * @param array $def 
     * @return mixed 
     * @throws IGKException 
     */

    public static function CreateFrom(array $def)
    {
        $inf = Activator::CreateNewInstance(static::class, $def);
        Activator::InitPrivatePropety(self::_InitializePrivatePropertiesCallback(), $inf, $def);
        return $inf;
    } 
}