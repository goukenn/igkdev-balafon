<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackagePropertyTrait.php
// @date: 20230330 12:58:39
namespace IGK\System\Npm\Traits;

/**
* auto generate doc.
* @package IGK\System\Npm\Traits
*/
trait JsonPackagePropertyTrait{
    /**
    * Name of name.
    * @var mixed
    */
    var $name;
    /**
    * Property: version.
    * @var mixed
    */
    var $version;
    /**
    * Property: description.
    * @var mixed
    */
    var $description;
    /**
    * Property: main.
    * @var mixed
    */
    var $main;
    /**
    * Property: scripts.
    * @var mixed
    */
    var $scripts;
    /**
    * Property: keywords.
    * @var mixed
    */
    var $keywords;
    /**
     * author definition 
     * @var ?string|object
     */
    var $author;
    /**
    * Property: license.
    * @var mixed
    */
    var $license;
    /**
    * Property: dependencies.
    * @var mixed
    */
    var $dependencies;
    /**
    * Property: dev dependencies.
    * @var mixed
    */
    var $devDependencies;
    /**
    * Property: module.
    * @var mixed
    */
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