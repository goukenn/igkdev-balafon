<?php
// @file: IGKHtmlHeaderLinkHost.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

/**
* Html header link host.
* @package IGK\System\Html
*/
final class HtmlHeaderLinkHost{
    /**
    * Collection of list.
    * @var mixed
    */
    private $m_list;
    /**
    * Properties: private link, shared link.
    * @var mixed
    */
    var $privateLink, $sharedLink;
    /**
     * Constructor.
     */
    public function __construct(){
        $this->m_list=array();
        $this->privateLink=array();
        $this->sharedLink=array();
    }
    /**
     * Adds a named link node to the host, optionally as a private link.
     *
     * @param string $name The unique name identifier for the link.
     * @param mixed  $node The link node to register.
     * @param bool   $temp Whether the link is temporary (private).
     */
    public function add($name, $node, $temp){
        if(isset($this->m_list[$name]))
            igk_die("link already referenced");
        $this->m_list[$name]=$node;
        if($temp){
            $this->m_privateLink[$name]=$node;
        }
    }
    /**
     * Removes all registered link nodes and clears the internal lists.
     */
    public function clearChilds(){
        foreach($this->m_list as $v){
            igk_html_rm($v);
        }
        $this->m_list=array();
        $this->privateLink=array();
        $this->sharedLink=array();
    }
    /**
     * Returns the link node registered under the given name.
     *
     * @param string $o The name of the link to retrieve.
     * @return mixed|null
     */
    public function getLink($o){
        return igk_getv($this->m_list, $o);
    }
}