<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatchPatternOutpuTreatmentListener.php
// @date: 20241104 11:48:37
namespace IGK\System\Text;


/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexMatchPatternOutpuTreatmentListener{
    function getOutput():?string;
}