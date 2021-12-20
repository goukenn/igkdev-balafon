<?php
// @file: HtmlItemAttribute.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\System\Html\Dom;

use IGK\System\Html\IHtmlGetValue;
use IGKObject;


abstract class HtmlItemAttribute extends IGKObject implements IHtmlGetValue{
    ///<summary></summary>
    ///<param name="option" default="null"></param>
    abstract function getValue($option=null);
}
