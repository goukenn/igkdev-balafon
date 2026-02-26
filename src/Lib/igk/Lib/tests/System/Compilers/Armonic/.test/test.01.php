<?php
namespace IGK\Tests\Systems\Compilers\Armonic\Demo;
// @author: C.A.D. BONDJE DOUE
// @filename: test.01.php
// @date: 20221020 11:25:10
// @desc:

/**
* My trait.
* @package IGK\Tests\Systems\Compilers\Armonic\Demo
*/
abstract class MyTrait{

    /**
    * Constant: data.
    * @var mixed
    */
    const data = "dsdf";

    /**
    * Property: jump.
    * @var mixed
    */
    protected $jump = "info";

    /**
    * Property: a.
    * @var mixed
    */
    var $a;

    /**
    * Property: b.
    * @var mixed
    */
    var $b = "";

    /**
    * Property: c.
    * @var mixed
    */
    var $c = [];

    /**
    * Property: h.
    * @var mixed
    */
    var $h = array("basic"=>8,  "find"=>8);

    /**
    * Constant: invoke.
    * @var mixed
    */
    const INVOKE = self::data + "presentation";
}