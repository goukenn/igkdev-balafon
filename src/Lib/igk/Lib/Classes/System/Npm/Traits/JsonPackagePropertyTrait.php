<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackagePropertyTrait.php
// @date: 20230330 12:58:39
namespace IGK\System\Npm\Traits;


///<summary></summary>
/**
* 
* @package IGK\System\Npm\Traits
*/
trait JsonPackagePropertyTrait{
    var $name;
    var $version;
    var $description;
    var $main;
    var $scripts;
    var $keywords;
    /**
     * author definition 
     * @var ?string|object
     */
    var $author;
    var $license;
    var $dependencies;
    var $devDependencies;

    var $module;

    /**
     * type of the module
     * @var ?string module|commonjs
     */
    var $type;
    /**
     * indicate that the package is private 
     * @var ?bool
     */
    var $private;

    /**
     * configuration setting
     * @var mixed
     */
    var $config;
}