<?php
// @author: C.A.D. BONDJE DOUE
// @filename: DomNodeBase.php
// @date: 20220803 13:48:56
// @desc: 


namespace IGK\System\Html\Dom;

use IGK\System\Html\HtmlInitNodeInfo;
use IGKObject;

/**
 * represent dom node.
 * @package IGK\System\Html\Dom
 */
abstract class DomNodeBase extends IGKObject{    
    /**
     * set property used to initialize the node
     * @param string $type 
     * @return mixed 
     */
    protected abstract function setInitNodeTypeInfo(HtmlInitNodeInfo $info);

    /**
     * get init node type info
     * @return null|HtmlInitNodeInfo 
     */
    public abstract function getInitNodeTypeInfo() : ?HtmlInitNodeInfo;

    /**
     * retrieve parent node 
     * @return ?static|mixed 
     */
    public abstract function getParentNode();
}