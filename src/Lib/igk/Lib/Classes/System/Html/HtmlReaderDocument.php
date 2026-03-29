<?php
// @file: HtmlReaderDocument.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\System\Html;
use IGK\Helper\SysUtils;
use IGK\System\Html\XML\XmlNode;
/**
* Html reader document.
* @package IGK\System\Html
*/
final class HtmlReaderDocument extends XmlNode
{
    /**
    * .ctr
    */
    public function __construct()
    {
        parent::__construct("DocumentToRender");
    }
    /**
    * Returns properties to serialize.
    */
    public function __sleep()
    {
        $t = [];
        return $t;
    }
    /**
    * Copy to.
    * @param mixed $target
    */
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
    /**
    * Rende ajx.
    * @param null|mixed $o
    */
    public function RendeAJX($o = null)
    {
        parent::renderAJX($o);
    }
    /**
    * Renders.
    * @param null|mixed $options
    */
    public function render($options = null)
    {
        $out = IGK_STR_EMPTY;
        foreach ($this->Childs as $k) {
            $out .= $k->render($options);
        }
        return $out;
    }
}