<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownFilterHost.php
// @date: 20260130 18:40:12
namespace IGK\System\IO\Markdown;

/**
* auto generate doc.
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
interface IMarkdownFilterHost{

    /**
    * Initializes Menu List.
    * @param mixed $i
    * @return ?int
    */
    function initMenuList($i);
    function getListTable();
    function getListTableNewIds():?int;

    /**
    * Popup bullet list.
    * @param string $root
    * @return string
    */
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

    /**
    * Lists Table Ref Count.
    * @return int
    */
    function listTableRefCount():int;

    /**
    * Returns Title Style Id.
    * @param int $level
    * @return ?string
    */
    function getTitleStyleId(int $level):?string; 
}