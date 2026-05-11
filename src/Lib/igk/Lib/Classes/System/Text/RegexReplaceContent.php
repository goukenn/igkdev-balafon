<?php
// @author: C.A.D. BONDJE DOUE
// @file: RegexReplaceContent.php
// @date: 20250126 17:49:52
namespace IGK\System\Text;
use Exception;
/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
class RegexReplaceContent{
    /**
    * Property: info.
    * @var mixed
    */
    var $info;
    /**
    * auto generate doc.
    * @var mixed
    */
    var $replaceListener;
    /**
    * auto generate doc.
    * @param mixed $replaceListener
    * @return string
    */
    public function replaceWith (string $source, RegexMatcherContainer $container, $replaceListener = null ){
        $offset = 0;
        $output = '';
        $v_coffset = $offset;
        $v_rp = $replaceListener ?? $this->replaceListener;
        while($g = $container->detect($source, $offset)){
            if ($e = $container->end($g, $source, $offset)){
                if(is_null($e->parentInfo)){
                    $output.= substr($source, $v_coffset, $e->from-$v_coffset);
                    $output.= $v_rp($e, $this->info);
                    $v_coffset = $e->to;
                } else {
                    igk_die("replace no implement - for sub context");
                }
            }
        }
        $output.= substr($source, $v_coffset);
        return $output;
    }
}