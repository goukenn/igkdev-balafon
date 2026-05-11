<?php
// @author: C.A.D. BONDJE DOUE
// @file: IRegexMatcherPatternContainer.php
// @date: 20250816 10:37:34
namespace IGK\System\Text;
/**
* auto generate doc.
* @package IGK\System\Text
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\Text
*/
interface IRegexMatcherPatternContainer{
    /**
    * auto generate doc.
    * @param int &$offset
    * @return mixed
    */
    function startMatch(?RegexDetectInfo $parentInfo, ?RegexDetectInfo & $info, string $source, int & $offset);
}