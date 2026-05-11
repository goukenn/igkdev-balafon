<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexMatcherContainerTreatment.php
// @date: 20260409 08:41:24
namespace IGK\System\Text;
use IGK\System\Console\Logger;

/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
class RegexMatcherContainerTreatment{
    /**
    * auto generate doc.
    * @var {handle:array,filter:?null,postfilter:?null}
    */
    var $listener;
    /**
    * auto generate doc.
    * @param string $src
    * @param RegexMatcherContainer $regex
    * @return void
    */
    public function treat(string $src,  RegexMatcherContainer $regex){
        $listener = $this->listener;
        if (!$listener){
            igk_die('missing listener');
        }
        $inf = $listener;
        $pos = 0;
        $is_debug = igk_is_debug('regex-treatment-debug') || igk_is_debug();
        while($g = $regex->detect($src, $pos)){
        if ($e = $regex->end($g, $src, $pos)){
            $id = $e->tokenID;
            $is_debug && Logger::info('regex-treament:'.$id. ' pos: '.$pos.' : '.json_encode($e->value));
            if ($fc = $inf->filter ){
                if ($fc($e, $inf, $src))continue;
            }
            if ($id && ($fc = igk_getv($inf->handle, $id))){
                $fc($e, $inf, $src);
            }
            if ($fc = $inf->postfilter){
                $fc($e, $inf, $src);
            }
        }
    }
    }
}