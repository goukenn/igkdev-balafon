<?php
// @author: C.A.D. BONDJE DOUE
// @file: JsonPackagePropertyTrait.php
// @date: 20230330 12:58:39
namespace IGK\System\Npm\Traits;
/**
* 
* @package IGK\System\Npm\Traits
*/
trait JsonPackagePropertyTrait{

    /**
    * auto generate doc.
    * @var mixed
    */
    var $name;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $version;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $description;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $main;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $scripts;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $keywords;
    /**
     * author definition 
     * @var ?string|object
     */
    var $author;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $license;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $dependencies;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $devDependencies;

    /**
    * auto generate doc.
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