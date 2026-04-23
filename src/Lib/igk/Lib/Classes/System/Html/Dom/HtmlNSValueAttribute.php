<?php
// @file: IGKNSValue.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;

/**
* Html nsvalue attribute.
* @package IGK\System\Html\Dom
*/
final class HtmlNSValueAttribute implements IHtmlGetValue{
    /**
    * Properties: n, ns.
    * @var mixed
    */
    private $m_n, $m_ns;
    /**
     * Constructor.
     *
     * @param mixed $n  The HTML node to check for namespace membership.
     * @param mixed $ns The namespace value to return when the node qualifies.
     */
    public function __construct($n, $ns){
        $this->m_ns=$ns;
        $this->m_n=$n;
    }
    /**
     * Returns a string representation including the namespace identifier.
     *
     * @return string
     */
    public function __toString(){
        return __CLASS__.":ns:".$this->m_ns;
    }
    /**
     * Returns the namespace value when the node belongs to the namespace, or null otherwise.
     *
     * @param mixed $options Optional rendering options.
     * @return mixed The namespace string or null.
     */
    public function getValue($options=null){
        if(igk_html_is_ns_child($this->m_n)){
            return $this->m_ns;
        }
        return null;
    }
}