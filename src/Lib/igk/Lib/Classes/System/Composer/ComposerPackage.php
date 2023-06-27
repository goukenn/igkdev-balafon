<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ComposerPackage.php
// @date: 20230414 15:52:41
// @desc: 

namespace IGK\System\Composer;

use IGK\Helper\Activator;
use IGK\System\Composer\Traits\ComposerPackageFileTrait;

/**
 * @package IGK\System\Composer
 */
class ComposerPackage{
    use ComposerPackageFileTrait;
    



    /**
     * 
     * @param string $file 
     * @return static|false 
     */
    public static function Load(string $file){
        $data = json_decode(file_get_contents($file)) ?? igk_die("no data in : $file");
        if ($c = ComposerPackageValidator::ValidateData($data, null, $errors)) {
            return Activator::CreateNewInstance(static::class, $c);
        }
        igk_environment()->last_error = $errors;
        return false;
    }
}
