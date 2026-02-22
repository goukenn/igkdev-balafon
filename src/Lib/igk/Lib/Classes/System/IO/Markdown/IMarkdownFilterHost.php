<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownFilterHost.php
// @date: 20260130 18:40:12
namespace IGK\System\IO\Markdown;


/**
* 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
interface IMarkdownFilterHost{
    function initMenuList($i);
    function getListTable();
    function getListTableNewIds():?int;
    function popupBulletList(string $root);
    /**
     * 
     * @param string $text 
     * @return string 
     */
    function prepareFormat(string $text):string;
    /**
     * escape litteral
     * @param string $text 
     * @return string 
     */
    function escape(string $text):string;
    function listTableRefCount():int;
    function getTitleStyleId(int $level):?string; 
}