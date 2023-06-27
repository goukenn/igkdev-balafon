<?php
// @file: IGKHtmlAttribExpressionNode.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlItemBase;
use IGK\System\Html\XML\XmlNode;

class HtmlAttribExpressionNode extends XmlNode
{
    var  $node_args;
    var  $target_node;
    ///<summary>Represente __construct function</summary>
    ///<param name="c"></param>
    ///<param name="context" default="null"></param>
    public function __construct(HtmlItemBase $cnode, array $c)
    {        
        parent::__construct(IGK_ENGINE_ATTR_EXPRESSION_NODE);
        $this->node_args = $c; 
        $this->target_node = $cnode; 
    }
    ///<summary>Represente getCanAddChild function</summary>
    public function getCanAddChild()
    {
        return false;
    }
    ///<summary>Represente loadingComplete function</summary>
    public function loadingComplete()
    { 
        $context = null;
        $m = $this->Attributes->to_array();
        $_p = [];
        $_g = explode("|", "*for|*visible");
        $context = $this->node_args ?? igk_get_attrib_raw_context($context);
        $p = $this->target_node;
        foreach ($m as $k => $t) {
            // ignore attribute binding
            if (in_array($k, $_g))
                continue;
            if ($k[0] == "*") {
                $t = igk_template_get_piped_value($t, $context);
                $k = ltrim($k, "*");
            }
            $_p[$k] = $t;
        }
        if (count($_p) > 0) {
            // + append attribute 
            $p->setAttributes($_p);  
        }
        $this->node_args = null;    
        $this->dispose(); 
    }
}
