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
    function didStateChanged();
    function didHandleOutput(& $isSingleDefinition, & $output);
    function title($text, int $level, ?string $slug = null): string;
    function par($text): string;
    function default($text): string;
    function filter($token_id, $value, bool $root, ?\Closure $callback=null, ?string $buffer=null);
}