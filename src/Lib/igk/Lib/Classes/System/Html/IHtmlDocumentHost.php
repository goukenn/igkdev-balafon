<?php
// @author: C.A.D. BONDJE DOUE
// @file: IHtmlDocumentHost.php
// @date: 20230102 22:00:15
namespace IGK\System\Html;

use IGK\System\Html\Dom\HtmlBodyNode;
use IGK\System\Html\Dom\HtmlHeadNode;

///<summary></summary>
/**
 * 
 * @package IGK\System\Html
 * @property ?bool $isTemplate enable template mode. 
 * @property ?bool $noCache disable document caching
 * @property ?bool $noCoreCss disable loading of core css
 * @property ?bool $noPowered disable powered by message
 * @property ?bool $noCoreScript disable core script rendering
 * @property ?bool $noFontInstall enable template mode. 
 */
interface IHtmlDocumentHost
{
    /**
     * set the document title
     * @param null|string $title 
     * @return mixed 
     */
    function setTitle(?string $title = null);

    function getBody(): ?HtmlBodyNode;
    /**
     * 
     * @return HtmlHeadNode 
     */
    function getHead(): ?HtmlHeadNode;
    /**
     * set the header color
     * @param null|string $color 
     * @return mixed 
     */
    function setHeaderColor(?string  $color );
}
