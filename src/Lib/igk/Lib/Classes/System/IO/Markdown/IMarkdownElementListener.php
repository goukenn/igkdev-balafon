<?php
// @author: C.A.D. BONDJE DOUE
// @file: IMarkdownElementListener.php
// @date: 20260130 18:39:27
namespace IGK\System\IO\Markdown;


/**
* 
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
interface IMarkdownElementListener
{

    /**
    * Did state changed.
    * @return string
    */
    function didStateChanged();
    function didHandleOutput(& $isSingleDefinition, & $output);
    function title(string $text, int $level, ?string $slug = null): string;

    /**
    * Par.
    * @param string $text
    * @return string
    */
    function par(string $text): string;

    /**
    * Default.
    * @param string $text
    * @return string
    */
    function default(string $text): string;

    /**
    * Filters.
    * @param mixed $token_id
    * @param mixed $value
    * @param bool $root
    * @param null|\Closure $callback
    * @param null|string $buffer
    */
    function filter($token_id, $value, bool $root, ?\Closure $callback=null, ?string $buffer=null);
}