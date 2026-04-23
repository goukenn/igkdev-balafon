<?php
// @file: .global.php
// @author: C.A.D. BONDJE DOUE
// @description:
// @copyright: igkdev © 2020
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com

/**
* auto generate doc.
* @param mixed $v the default value is 1
*/
function igk_designer_off($doc, $v=1){
    $doc->setParam("sys://designMode/off", $v ? 1: null);
}