<?php
// @author: C.A.D. BONDJE DOUE
// @file: ArticleHelper.php
// @date: 20230425 08:47:22
namespace IGK\Helper;

use IGK\Controllers\BaseController;
use IGK\Controllers\SysDbController;

///<summary></summary>
/**
* 
* @package IGK\Helpers
*/
class ArticleHelper{
    /**
     * resolve article
     * @param BaseController $ctrl 
     * @param mixed $article 
     * @return mixed 
     */
    public static function ResolveGetArticle(BaseController $ctrl, $article){
        $g =[$ctrl];
        if ($ctrl != SysDbController::ctrl()){
            $g[] = $ctrl;
        }
        while(count($g)){
            $ctrl = array_shift($g);
            $farticle = $ctrl->getArticle($article);
            if (!file_exists($farticle)){
                $farticle = null;
                continue;
            }
        }
        return $farticle;
    }
}