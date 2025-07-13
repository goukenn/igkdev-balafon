<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectHandler.php
// @date: 20250702 08:30:32
namespace IGK\System\Text;
use Exception;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexDetectHandler{
    /**
     * 
     * @var mixed
     */
    var $startTokenListener;
    /**
     * 
     * @var ?callable($e)
     */
    var $itemTokenListener;
    private $m_marker;
    /**
     * 
     * @var RegexMatcherContainer
     */
    public $regex;
    public function __construct(RegexMatcherContainer $regex){
        $this->regex = $regex;
        $this->m_marker = new RegexMatcherInitMarker;
    }
    /**
     * 
     * @param mixed $src 
     * @param callable $callable 
     * @param null|callable $preload 
     * @return void 
     * @throws Exception 
     */
    public function detect($src, callable $callable, ?callable $preload=null){
        $regex = $this->regex;
        $pos = 0;
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
}