<?php 
 
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