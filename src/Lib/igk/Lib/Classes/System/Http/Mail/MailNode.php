<?php
// @author: C.A.D. BONDJE DOUE
// @file: MailNode.php
// @date: 20250427 08:38:47
namespace IGK\System\Http\Mail;
/**
* auto generate doc.
* @package IGK\System\Http\Mail
* @author C.A.D. BONDJE DOUE
*/
class MailNode extends MailNodeBase
{
    /**
    * Property: inline style.
    * @var mixed
    */
    private $m_inline_style = '';
    /**
    * Property: render options.
    * @var mixed
    */
    private $m_render_options;
    /**
    * Property: resolver.
    * @var mixed
    */
    private $m_resolver;
    /**
    * .ctr
    * @param mixed $options
    * @param mixed $resolver
    * @param null|string $tagname
    */
    public function __construct($options, $resolver, ?string $tagname = null)
    {
        parent::__construct($tagname);
        $this->m_render_options = $options;
        $this->m_resolver = $resolver;
    }
    /**
    * Sets Class.
    * @param mixed $value
    */
    public function setClass($value){
        // no class preview
    }
    /**
    * auto generate doc.
    * @param mixed $v
    * @return
    */
    private function _get_style($v)
    {
        return implode(' ',   array_map([$this, '_class_to_style'], array_filter(explode(' ', $v . ''))));
    }
    /**
    * auto generate doc.
    * @param mixed $i
    * @return
    */
    private function _class_to_style($i)
    {
        $cc = $this->m_resolver;
        return $cc($i);
    }
    /**
    * Access offset set.
    * @param mixed $n
    * @param mixed $v
    */
    protected function _access_offsetSet($n, $v)
    {
        switch ($n) {
            case 'class':
                $v_style = parent::_access_OffsetGet('style');
                $v_class = implode(' ', array_filter([
                    $this->_get_style($this->tagname),
                    $this->_get_style($v)
                ]));
                $this->m_inline_style = $v_style . $v_class;
                parent::offsetSet('style', new MailStyleValue($this->m_inline_style));
                return true;
        }
        return parent::_access_OffsetSet($n, $v);
    }
    /**
    * Access offset get.
    * @param mixed $k
    */
    protected function _access_OffsetGet($k)
    {
        if ($k == 'style') {
            return $this->m_inline_style;
        }
        return parent::_access_OffsetGet($k);
    }
}