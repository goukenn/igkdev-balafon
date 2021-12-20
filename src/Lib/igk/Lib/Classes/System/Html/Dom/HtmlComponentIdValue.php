<?php
// @file: IGKHtmlComponentIdValue.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html\Dom;



final class HtmlComponentIdValue implements IHtmlGetValue{
    private $m_host;
    ///<summary></summary>
    ///<param name="host"></param>
    public function __construct($host){
        $this->m_host=$host;
    }
    ///<summary>display value</summary>
    public function __toString(){
        return $this->getValue();
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function getValue($options=null){
        if(method_exists($this->m_host, "getComponentId"))
            return $this->m_host->getComponentId();
        $ctrl=igk_getctrl(IGK_COMPONENT_MANAGER_CTRL, true);
        return $ctrl->getComponentId($this->m_host);
    }
}
