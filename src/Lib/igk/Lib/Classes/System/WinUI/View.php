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
    public function __construct(?array $options = null)
    {   
        $this->init($options);
    }
    public function init(?array $options=null){
        // init the view
        if ($options){
            foreach($this as $k=>$v){
                $this->$k = igk_getv($options, $k, $v);
            }
        }
    }
    public function reset(){
        foreach($this as $k=>$v){
            $this->$k = null;
        }
    }
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
     */
    public function view(HtmlNode $n){
        // implement to build custom view
    }
}