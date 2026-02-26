<?php
// @author: C.A.D. BONDJE DOUE
// @file: PostInstallApplication.php
// @date: 20260218 16:46:41
namespace IGK\Composer;

use IGKApplicationBase;

/**
* composer post installation
* @package IGK\Composer
* @author C.A.D. BONDJE DOUE
*/
class PostInstallApplication extends IGKApplicationBase{

    /**
    * auto generate doc.
    */
    public function bootstrap()
    {        
    }

    /**
    * auto generate doc.
    * @param string $entryfile
    * @param mixed $render
    */
    public function run(string $entryfile, $render = 1)
    {        
    } 
}