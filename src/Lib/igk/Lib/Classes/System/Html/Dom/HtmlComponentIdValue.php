<?php
// @file: IGKHtmlComponentIdValue.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;
final class HtmlComponentIdValue implements IHtmlGetValue{
    private $m_host;
    /**
     * Constructor.
     *
     * @param mixed $host The host object whose component ID will be resolved.
     */
    public function __construct($host){
        $this->m_host=$host;
    }
    /**
     * Returns the component ID as a string.
     *
     * @return string
     */
    public function __toString(){
        return $this->getValue();
    }
    /**
     * Resolves and returns the component ID for the host object.
     *
     * @param mixed $options Optional rendering options.
     * @return mixed The component ID string.
     */
    public function getValue($options=null){
        if(method_exists($this->m_host, "getComponentId"))
            return $this->m_host->getComponentId();
        $ctrl=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
        return $ctrl->getComponentId($this->m_host);
    }
}