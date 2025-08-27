<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatcherPatternContainer.php
// @date: 20250816 10:37:34
namespace IGK\System\Text;


/**
* 
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
interface IRegexMatcherPatternContainer{
    /**
     * 
     * @param null|RegexDetectInfo $parentInfo 
     * @param null|RegexDetectInfo &$info 
     * @param string $source 
     * @param int &$offset 
     * @return mixed 
     */
    function startMatch(?RegexDetectInfo $parentInfo, ?RegexDetectInfo & $info, string $source, int & $offset);
}