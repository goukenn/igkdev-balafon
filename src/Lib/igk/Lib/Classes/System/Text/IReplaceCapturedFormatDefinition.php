<?php
// @author: C.A.D. BONDJE DOUE
// @file: IReplaceCapturedFormatDefinition.php
// @date: 20250730 19:31:44
namespace IGK\System\Text;


/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
* @property ?array[] $beginCaptures
* @property ?array[] $endCaptures
* @property ?array[] $captures
* @property bool $isDirty
* @property bool $closeBlock
* @property bool $lineFeed;
* @property mixed $match
*/
interface IReplaceCapturedFormatDefinition extends IRegexCaptureInfo{ 
    function getHasSubChildren():bool;
}