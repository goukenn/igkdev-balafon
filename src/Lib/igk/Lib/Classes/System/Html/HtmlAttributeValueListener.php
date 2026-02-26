<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlAttributeValueListener.php
// @date: 20230313 18:10:13
namespace IGK\System\Html;
/**
* 
* @package IGK\System\Html
*/
class HtmlAttributeValueListener implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_listener;

    /**
    * .ctr
    * @param callable $listener
    */
    public function __construct(callable $listener){
        $this->m_listener = $listener;
    }

    /**
    * auto generate doc.
    * @param null|mixed $options
    */
    public function getValue($options = null)
    {
        if ($fc = $this->m_listener){
            return $fc($options);
        }
    }
}