<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexDetectHandler.php
// @date: 20250702 08:30:32
namespace IGK\System\Text;


/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
class RegexDetectHandler{
    /**
     * 
     * @var RegexMatcherContainer
     */
    public $regex;
    public function __construct(RegexMatcherContainer $regex)
    {
        $this->regex = $regex;
    }
    public function detect($src, callable $callable, ?callable $preload=null){
        $regex = $this->regex;
        $pos = 0;
        while($g = $regex->detect($src, $pos)){
            if ($preload){
                $preload($g, $src, $pos);
            }
            if ($e = $regex->end($g, $src, $pos)){
                $callable($e, $g, $src, $pos);
            }
        }
    }
}