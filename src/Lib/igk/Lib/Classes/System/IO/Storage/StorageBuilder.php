<?php
// @author: C.A.D. BONDJE DOUE
// @file: StorageBuilder.php
// @date: 20230305 19:15:19
namespace IGK\System\IO\Storage;

use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\System\IO\Storage
*/
class StorageBuilder implements IStorageBuilder{
    var $storage;

    var $prefix;

    public function __construct(IStorage $storage, string $prefix)
    {
        $this->storage = $storage;
        $this->prefix = $prefix;
    }
    /**
     * @param string|array $file 
     * @param bool $ovewrite 
     * @return IStorageInfo 
     */
    public function store($file, bool $ovewrite=false, ?string $type=null): ?IStorageInfo{
        if (!is_string($file) && is_array($file)){
            if (count($file)>1)
                list($file, $type) = $file;
        }
        if ($path = $this->storage->store($file, $ovewrite, $type)){
            $ext = igk_io_path_ext($file);
            $info = new StorageInfo;
            $info->path = Path::Combine($this->prefix, $path);
            $info->type = $type ?? igk_io_mimetype($ext, 'image/jpeg'); 
            return $info;
        }
    }
    public function delete(string $path):bool{
        return $this->storage->delete($path);
    }
    /**
     * string or array
     * @param mixed $file 
     * @param bool $ovewrite 
     * @param null|string $type 
     * @return IStorageInfo 
     */
    public function __invoke($file, bool $ovewrite=false, ?string $type=null){
      
        return $this->store($file, $ovewrite, $type);
    }
}