<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectHandler.php
// @date: 20250702 08:30:32
namespace IGK\System\Text;
use Exception;
use IGK\Helper\Activator;

/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
class RegexDetectHandler{
    /**
    * auto generate doc.
    * @var mixed
    */
    var $startTokenListener;
    /**
    * auto generate doc.
    * @var ?callable($e
    */
    var $itemTokenListener;
    /**
    * Property: marker.
    * @var mixed
    */
    private $m_marker;
    /**
    * auto generate doc.
    * @var RegexMatcherContainer
    */
    public $regex;
    /**
    * .ctr
    * @param RegexMatcherContainer $regex
    */
    public function __construct(RegexMatcherContainer $regex){
        $this->regex = $regex;
        $this->m_marker = new RegexMatcherInitMarker;
    }
    /**
    * Handle detect.
    * @param string $src
    * @param int & $pos
    * @param null|callable $preload
    * @param null|callable $callable
    */
    protected function _handleDetect(string $src,int & $pos, ?callable $preload, ?callable $callable){
         $regex = $this->regex;
         while($g = $regex->detect($src, $pos)){
            if ($preload){
                $preload($g, $src, $pos);
            }
            if ($fc = $this->startTokenListener){
                $fc($g, $src, $pos); 
            }
            if ($e = $regex->end($g, $src, $pos)){
                if ($fc = $this->itemTokenListener){
                    if ($this->m_marker->mark($e, $src, $pos, $fc)){
                        $fc($e, $src, $pos); 
                    }
                }
                $callable($e, $g, $src, $pos);
            }
        }
    }
    /**
     * detect ussing general handler 
     * @param string $src 
     * @param callable $callable 
     * @param null|callable $preload 
     * @return void 
     * @throws Exception 
     */
    public function detect(string $src, callable $callable, ?callable $preload=null){
        $regex = $this->regex;
        $pos = 0;
        $engineInfo = Activator::CreateNewInstance(RegexMatcherEngineInfo::class, [
            'type'=>'treat',
            'end_token_id'=>'detect-treat',
            'callable'=>function ($e, & $pos, & $src)use($callable){
                /**
                 * mark RegexCapture as a trailingEnd
                 */
                $e->trailingEnd = true;
                $callable($e, null, $src, $pos); 
            }
        ]);
        $v_bckr = $regex->getEngineInfo();
        $regex->setEngineInfo($engineInfo);
        while($g = $regex->detect($src, $pos)){
            if ($preload){
                $preload($g, $src, $pos);
            }
            if ($fc = $this->startTokenListener){
                $fc($g, $src, $pos); 
            }
            if ($e = $regex->end($g, $src, $pos)){
                if ($fc = $this->itemTokenListener){
                    if ($this->m_marker->mark($e, $src, $pos, $fc)){
                        $fc($e, $src, $pos); 
                    }
                }
                $callable($e, $g, $src, $pos);
            }
        }
        $regex->setEngineInfo($v_bckr);
    }
}