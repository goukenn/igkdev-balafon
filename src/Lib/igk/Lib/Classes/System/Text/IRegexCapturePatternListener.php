<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexCapturePatternListener.php
// @date: 20241107 09:29:28
namespace IGK\System\Text;


/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexCapturePatternListener{
    function treatPattern(RegexTreatCapture $cap, $patterns);
}