<?php
// @author: C.A.D. BONDJE DOUE
// @file: FormatterServiceContainer.php
// @date: 20250809 16:05:18
namespace IGK\System\Text\Formatters;

use igk\phpFormatter\Formatters\HtmlFormatter;
use IGK\Services\IAppService;
use IGK\Services\IAppServiceContainer;
use IGK\System\Services\Traits\ServiceContainerTrait;
use IGK\System\Services\Traits\ServicePropertyTrait;
use IGK\System\Text\RegexMatcherContainer;

/**
* 
* @package IGK\System\Text\Formatters
* @author C.A.D. BONDJE DOUE
*/
class FormatterServiceContainer implements IAppServiceContainer{
    use ServiceContainerTrait; 
    private $m_resolvedScope = [];
    use ServicePropertyTrait;
    /**
     * init service 
     * @param mixed $configs 
     * @return bool 
     */
    public function init($configs = null): bool { 
        return true;
    }
    /**
     * 
     * @param mixed $scopeName 
     * @return void 
     */
    public function getFormatRegexContainer(string $scopeName){

    }
    /**
     * 
     */
    public function resolveFormat(string $scopeName){
        if($scopeName=='source.html'){
            $regex = new RegexMatcherContainer;
            HtmlFormatter::InitFormatter($regex);   
            $formatter = igk_app()->getService('formatter.html'); 
            $regex->enginePatternListener = function()use($scopeName, $formatter){
                static $litteral;
                if (is_null($litteral)){
                    $litteral = [];
                }
                if (!isset($litteral[$scopeName])){
                    $cl = $formatter->resolveEngineClassName();
                    $litteral[$scopeName] = new $cl;
                }
                return $litteral[$scopeName];
            };      
            return $regex;
        } 
        return null;
    }
}