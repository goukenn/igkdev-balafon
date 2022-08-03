<?php
// @author: C.A.D. BONDJE DOUE
// @filename: FileSystem.7.php
// @date: 20220803 13:48:55
// @desc: 
 
 
namespace IGK\System\IO;

use IGK\System\Exceptions\ArgumentNotValidException; 
require_once __DIR__."/CoreFileSystem.php";
/**
 * file system helper 
 */
class FileSystem extends CoreFileSystem{
    public function __construct(string $dir){
        if (!file_exists($dir)){
            throw new ArgumentNotValidException("dir");
        }
        $this->path = $dir;
    }
}