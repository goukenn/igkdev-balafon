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

///<summary></summary>
/**
 * 
 * @package IGK\System\Text
 * @author C.A.D. BONDJE DOUE
 */
class RegexCaptureInfo extends IGKObject implements IRegexCaptureInfo
{
    use ActivatorPrivateInitProperty;
    private $pos;
    private $to;
    private $value; 

    var $childs;
    var $data;
    var $_dynamic;

    public function getisRoot(): bool { return true;}

    public function getisRootCaptured(): bool { return true;}

    public function getPos(){
        return $this->pos;
    }
    public function getTo(){
        return $this->to;
    }
    public function getValue(){
        return $this->value;
    }
    public function __get($n){
        if ($n === RegexTreatCapture::MARK_KEY){
            if ($this->_dynamic){
                return igk_getv($this->_dynamic, $n);
            }
            return 0;
        }  
        return parent::__get($n);
    }
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
 
