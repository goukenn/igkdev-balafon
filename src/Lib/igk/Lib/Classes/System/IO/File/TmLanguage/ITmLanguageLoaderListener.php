<?php
// @author: C.A.D. BONDJE DOUE
// @file: ITmLanguageLoaderListener.php
// @date: 20241107 04:53:12
namespace IGK\System\IO\File\TmLanguage;
use IGK\System\Text\RegexMatcherPattern;
/**
* 
* @package IGK\System\IO\File\TmLanguage
* @author C.A.D. BONDJE DOUE
*/
/**
* auto generate doc.
* @package IGK\System\IO\File\TmLanguage
*/
interface ITmLanguageLoaderListener{
    /**
     * use to create a definition pattern
     * @param mixed $definition 
     * @return RegexMatcherPattern 
     */
    function createPattern($definition) : RegexMatcherPattern;
    /**
    * auto generate doc.
    * @param mixed $repository
    * @return mixed
    */
    function loadComplete($definition, $container, $repository);
}