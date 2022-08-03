<?php
// @author: C.A.D. BONDJE DOUE
// @filename: HtmlVisibleNode.php
// @date: 20220803 13:48:56
// @desc: 

namespace IGK\System\Html\Dom;

/**
 * visibility node
 * @package IGK\System\Html\Dom
 */
class HtmlVisibleNode extends HtmlNode{
    private $m_callback;
    function getCanRenderTag()
    {
        return false;
    }
    /**
     * construct visible node
     * @param bool|callable $callback 
     * @return void 
     */
    function __construct($callback)
    {
        parent::__construct("igk:visible");
        $this->m_callback = $callback;
    }
    public function getIsVisible()
    {
        if ($fc = $this->m_callback){
            if (is_callable($fc))
                return $fc($this);

            if (is_bool($fc))
                return $fc;
        }
        return false;
    }    
}