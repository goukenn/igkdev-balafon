<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlAttributeValueListener.php
// @date: 20230313 18:10:13
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlAttributeValueListener implements IHtmlGetValue{
    private $m_listener;
    public function __construct(callable $listener){
        $this->m_listener = $listener;
    }
    public function getValue($options = null)
    {
        if ($fc = $this->m_listener){
            return $fc($options);
        }
    }
}