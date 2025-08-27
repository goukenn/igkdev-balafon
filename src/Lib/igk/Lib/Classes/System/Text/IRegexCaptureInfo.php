<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexCaptureInfo.php
// @date: 20241106 11:44:37
namespace IGK\System\Text;
/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
* @property int $pos start position 
* @property int $to end position
* @property string $value original value
* @property string $data treated value
* @property string $tag treated value
* @property string $match treated value
* @property int $from treated value
* @property ?string $tokenID the token identification
*/
interface IRegexCaptureInfo{
    function getisRoot():bool;
    function getisRootCaptured():bool;
}