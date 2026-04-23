<?php
// @author: C.A.D. BONDJE DOUE
// @file: IReplaceCapturedFormatDefinition.php
// @date: 20250730 19:31:44
namespace IGK\System\Text;

/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
* @property mixed $match
*/
interface IReplaceCapturedFormatDefinition extends IRegexCaptureInfo{
    /**
    * Returns Has Sub Children.
    * @return bool
    */
    function getHasSubChildren():bool;
}