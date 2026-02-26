<?php
// @file: IGKFrameScript.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
use IGK\System\Html\IHtmlGetValue;

/**
* auto generate doc.
*/
final class IGKFrameScript implements IHtmlGetValue{

    /**
    * auto generate doc.
    * @var mixed
    */
    private $m_type;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $owner;
    /**
     * Constructor.
     * @param mixed $owner The owner node of this frame script.
     * @param string $type The frame script type identifier.
     */

    public function __construct($owner, $type="f"){
        $this->owner=$owner;
        $this->m_type=$type;
    }
    /**
     * Returns the JavaScript initialization string for the frame box.
     * @param mixed $option Optional rendering option.
     * @return string The formatted JavaScript call string.
     */

    public function getValue($option=null){
        $n=IGK_STR_EMPTY;
        switch($n){
            case "c":
            $n="initconfirm";
            break;
            case "f":default:
            $n="init";
            break;
        }
        return igk_get_string_format("igk.winui.frameBox.{$n}({0}],{1});", igk_getsv($this->owner->Width ? '"'.$this->owner->Width.'"': null, 'null'), igk_getsv($this->owner->Height ? '"'.$this->owner->Height.'"': null, 'null'));
    }
}
