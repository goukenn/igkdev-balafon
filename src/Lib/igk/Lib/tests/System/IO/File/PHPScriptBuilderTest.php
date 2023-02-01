<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PHPScriptBuilderTest.php
// @date: 20220624 08:58:33
// @desc: 

namespace IGK\Tests\System\IO\File;

use IGK\System\IO\File\PHPScriptBuilder;
use IGK\Tests\BaseTestCase;

/**
 * testing script builder 
 * @package IGK\Tests\System\IO\File
 */
class PHPScriptBuilderTest extends BaseTestCase
{


    public function test_builder_gen()
    {
        $src = <<<'PHP'
<?php
namespace dummy;

use AppTestProject;
use ArrayAccess;
use IGK\System\Database\Factories\FactoryBase as Factory;

///<summary>Factory base</summary>
/**
* Factory base
* @package dummy
*/
abstract class FactoryBase extends Factory implements ArrayAccess{

}
PHP;


        $builder = new PHPScriptBuilder();
        $builder->no_header_comment = true;
        $builder->type("class")
            ->name("FactoryBase")
            ->namespace("dummy")
            ->author(IGK_AUTHOR)
            ->uses([
                \AppTestProject::class,
                \IGK\System\Database\Factories\FactoryBase::class => "Factory"
            ])
            ->desc("factory base")
            ->doc("Factory base")
            ->class_modifier("abstract")
            // ->extends('Factory')
            ->extends(\IGK\System\Database\Factories\FactoryBase::class)
            ->implements(\ArrayAccess::class)
            ->defs(implode(
                "\n",
                []
            ));

        $this->assertEquals($src, $builder->render(), "-- ! --");
    }

    public function test_gen_factory()
    {
        $builder = new PHPScriptBuilder();
        $builder->no_header_comment = true;
        $builder->type("class")
            ->name("FactoryBase")
            ->namespace("dummy")
            ->author(IGK_AUTHOR)
            ->uses([
                \AppTestProject::class,
                \IGK\System\Database\Factories\FactoryBase::class => "Factory"
            ])
            ->desc("factory base")
            ->doc("Factory base")
            ->class_modifier("abstract")
            ->extends(\IGK\System\Database\Factories\FactoryBase::class)
            ->implements(\ArrayAccess::class)
            ->defs(implode(
                "\n",
                []
            ));
        $src = implode(
            "\n",
            [
                "<?php",
                "namespace dummy;",
                "",
                "use AppTestProject;",
                "use ArrayAccess;",
                "use IGK\System\Database\Factories\FactoryBase as Factory;",
                "",
                "///<summary>Factory base</summary>",
                "/**",
                "* Factory base",
                "* @package dummy",
                "*/",
                "abstract class FactoryBase extends Factory implements ArrayAccess{",
                "",
                "}"
            ]
        );
        $this->assertEquals($src, $builder->render(), "generate factory not valid failed.");
    }
}
