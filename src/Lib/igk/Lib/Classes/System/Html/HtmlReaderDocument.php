<?php
// @file: HtmlReaderDocument.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;

use IGK\Helper\SysUtils;
use IGK\System\Html\XML\XmlNode;

final class HtmlReaderDocument extends XmlNode
{
    ///<summary></summary>
    public function __construct()
    {
        parent::__construct("DocumentToRender");
    }
    ///<summary></summary>
    public function __sleep()
    {
        $t = [];
        return $t;
    }
    ///<summary> copy the current node to destination</summary>
    public function CopyTo($target)
    {
        $t = ($c = $this->getChilds()) ? SysUtils::ToArray($c) : null;
        // $this->__rm_childs(__FUNCTION__);
        if ($t) foreach ($t as $k) {
            if ($k == null)
                continue;
            $target->add($k);
        }
    }
    ///<summary></summary>
    ///<param name="o" default="null"></param>
    public function RendeAJX($o = null)
    {
        parent::renderAJX($o);
    }
    ///<summary></summary>
    ///<param name="options" default="null"></param>
    public function render($options = null)
    {
        $out = IGK_STR_EMPTY;
        foreach ($this->Childs as $k) {
            $out .= $k->render($options);
        }
        return $out;
    }
}
