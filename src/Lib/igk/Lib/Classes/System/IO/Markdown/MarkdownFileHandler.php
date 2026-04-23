<?php
// @author: C.A.D. BONDJE DOUE
// @file: MardownFileHandler.php
// @date: 20260212 16:40:53
namespace IGK\System\IO\Markdown;
use IGK\System\IO\FileHandler;

/**
* auto generate doc.
* @package IGK\System\IO\Markdown
* @author C.A.D. BONDJE DOUE
*/
class MarkdownFileHandler extends FileHandler{
    /**
     * transform to string 
     * @param string $content 
     * @return null|string 
     */
    public function transform(string $content): ?string
    {
        $n = igk_create_notagnode();
        $n->markdown($content);
        return $n->render();
    }
}