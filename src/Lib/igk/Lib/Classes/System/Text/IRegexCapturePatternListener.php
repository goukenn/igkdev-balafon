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

    /**
    * auto generate doc.
    * @param RegexTreatCapture $cap
    * @param mixed $patterns
    */
    function treatPattern(RegexTreatCapture $cap, $patterns);
}