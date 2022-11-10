<?php

// @author: C.A.D. BONDJE DOUE
// @filename: BalafonCompileTest.php
// @date: 20220830 17:44:36
// @desc: 
// phpunit -c phpunit.xml.dist src/application/Lib/igk/Lib/tests/System/Compilers/BalafonCompileTest.php
namespace IGK\Tests\System\Compilers;

use IGK\Controllers\BaseController;
use IGK\Helper\IO;
use IGK\Helper\StringUtility;
use IGK\Helper\ViewHelper;
use IGK\System\Exceptions\EnvironmentArrayException;
use IGK\System\Html\Dom\HtmlDocumentNode;
use IGK\System\Html\Dom\HtmlNode;
use IGK\System\IO\StringBuilder;
use IGK\System\Runtime\Compiler\BalafonViewCompileInstruction;
use IGK\System\Runtime\Compiler\BalafonViewCompiler;
use IGK\System\Runtime\Compiler\BalafonViewCompilerOptions;
use IGK\System\Runtime\Compiler\BalafonViewCompilerUtility;
use IGK\System\Runtime\Compiler\Html\CompilerNodeModifyDetector;
use IGK\System\Runtime\Compiler\Html\ConditionBlockNode;
use IGK\System\ViewEnvironmentArgs;
use IGK\System\WinUI\PageLayout;
use IGK\Tests\BaseTestCase;
use IGK\Tests\Controllers\TestController;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use PHPUnit\Framework\ExpectationFailedException;
use IGKException;


/**
 * test compiler ... 
 * @package IGK\Tests\System\Compilers
 */
abstract class BalafonCompileBase extends BaseTestCase
{
    protected static $sm_tempdir;
    public static function tearDownAfterClass(): void
    {
        IO::RmDir(self::$sm_tempdir);
    }
    public static function setUpBeforeClass(): void
    {
        $sdir = sys_get_temp_dir() . "/testCompiler";
        IO::CreateDir($sdir);
        self::$sm_tempdir = $sdir;
    }
}