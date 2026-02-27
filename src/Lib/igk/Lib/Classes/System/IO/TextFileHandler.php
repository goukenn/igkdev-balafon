<?php
// @author: C.A.D. BONDJE DOUE
// @file: TextFileHandler.php
// @date: 20260212 16:49:26
namespace IGK\System\IO;

/**
* auto generate doc.
* @package IGK\System\IO
* @author C.A.D. BONDJE DOUE
*/
class TextFileHandler extends FileHandler{
    /**
     * just transform text file 
     * @param string $content 
     * @return mixed 
     */
    public function transform(string $content)
    {
        return $content;
    }

}