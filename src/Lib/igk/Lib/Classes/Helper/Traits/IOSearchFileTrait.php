<?php
// @author: C.A.D. BONDJE DOUE
// @file: IOSearchFileTrait.php
// @date: 20230323 13:08:20
namespace IGK\Helper\Traits;

use IGK\System\IO\Path;

///<summary></summary>
/**
* 
* @package IGK\Helper\Traits
*/
trait IOSearchFileTrait{
 /**
     * search for file 
     * @param string $path 
     * @param null|array $extension 
     * @param string $index_file 
     * @return string|null 
     */
    public static function SearchFile(string $path, ?array $extension, $index_file='index.php'){
        if (is_dir($path)){
            $s = Path::Combine($path, $index_file);
            if (file_exists($s)){
                return $s;
            }
        }
        $sb = array_merge([""], $extension ?? []);
        while(count($sb)>0){
            $q = $path.array_shift($sb);
            if (file_exists($q)){
                return $q;
            }
        }
        return null;
    }
}