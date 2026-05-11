<?php
// @author: C.A.D. BONDJE DOUE
// @filename: View.php
// @date: 20220803 13:48:55
// @desc: 
namespace IGK\System\WinUI;
use IGK\System\Html\Dom\HtmlNode;

/**
 * default viw block
 * @package IGK\System\WinUI
 */
class View{
    /**
    * .ctr
    * @param null|array $options
    */
    public function __construct(?array $options = null)
    {   
        $this->init($options);
    }
    /**
    * Initializes.
    * @param null|array $options
    */
    public function init(?array $options=null){
        if ($options){
            foreach($this as $k=>$v){
                $this->$k = igk_getv($options, $k, $v);
            }
        }
    }
    /**
    * Resets.
    */
    public function reset(){
        foreach($this as $k=>$v){
            $this->$k = null;
        }
    }
    /**
    * Called when an object is used as a function.
    */
    public function __invoke()
    {
        if (($args = func_get_args()) && 
             ($args[0] instanceof HtmlNode)
        ){
            $this->view($args[0], array_slice($args,1));
        }
    }
    /**
    * view of the compoent
    * @param HtmlNode $n
    */
    public function view(HtmlNode $n){
    }
}