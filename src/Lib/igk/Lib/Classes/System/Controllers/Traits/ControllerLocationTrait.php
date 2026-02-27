<?php
// @author: C.A.D. BONDJE DOUE
// @file: ControllerLocationTrait.php
// @date: 20230316 09:12:42
namespace IGK\System\Controllers\Traits;
use IGK\System\IO\Path;

/**
* auto generate doc.
* @package IGK\System\Controllers\Traits
*/
trait ControllerLocationTrait{

    /**
    * Returns View Dir.
    */
    public function getViewDir()
    {
        return Path::Combine($this->getDeclaredDir(), IGK_VIEW_FOLDER);
    }

    /**
    * Returns Articles Dir.
    */
    public function getArticlesDir()
    {
        return Path::Combine($this->getDeclaredDir(), IGK_ARTICLES_FOLDER);
    }

    /**
    * Returns Scripts Dir.
    */
    public function getScriptsDir()
    {
        return Path::Combine($this->getDeclaredDir(), IGK_SCRIPT_FOLDER);
    }
}