<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ViewUriHelper.php
// @date: 20220524 09:02:09
// @desc: view uri helper


namespace IGK\Helper;
use IGK\Controllers\BaseController;

/**
 * uri helper controller
 */
class ViewUriHelper{
    private $controller;
    private $fname;
    public function __construct(BaseController $controller, $fname)
    {
        $this->controller = $controller;
        $this->fname = $fname;
    }
    public function uri(?string $path=""){
        return $this->controller->getAppUri($this->fname.$path);
    }
}