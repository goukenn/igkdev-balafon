<?php
// @author: C.A.D. BONDJE DOUE
// @file: HtmlAttributeExpression.php
// @date: 20221109 14:22:51
namespace IGK\System\Html;


///<summary></summary>
/**
* 
* @package IGK\System\Html
*/
class HtmlAttributeExpression implements IHtmlGetValue{
    private $m_data;
    public function __construct(string $data)
    {
        $this->m_data = $data; 
    }
    /**
     * prepend string data 
     * @param string $data 
     * @return void 
     */
    public function prepend(string $data){
        $this->m_data = $data.$this->m_data;
    }
    /**
     * prepend string data 
     * @param string $data 
     * @return void 
     */
    public function append(string $data){
        $this->m_data = $this->m_data.$data;
    }
    /**
     * get expression attribute
     * @param mixed $options 
     * @return null|string 
     */
    public function getValue($options = null):?string{
        return $this->m_data;
    }
    public function __toString(){
        return $this->getValue(null);
    }
}