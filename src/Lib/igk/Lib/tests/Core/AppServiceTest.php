<?php
// @author: C.A.D. BONDJE DOUE
// @file: AppServiceTest.php
// @date: 20251224 09:41:56
namespace IGK\Tests\Core;

use IGK\Services\IAppService;
use IGK\System\DependencyInjection\LifeTime;
use IGK\System\Services\Traits\ServicePropertyTrait;
use IGK\Tests\BaseTestCase;
use IGKServices;

/**
* 
* @package IGK\Tests\Core
* @author C.A.D. BONDJE DOUE
*/
class AppServiceTest extends BaseTestCase{

    /**
    * auto generate doc.
    * @var mixed
    */
    private static $sm_baseServices;

    /**
    * auto generate doc.
    * @return void
    */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        $i = IGKServices::getInstance();
        self::$sm_baseServices = $i->services();
    }

    /**
    * auto generate doc.
    */
    public function test_services_clear(){
        
        $i = IGKServices::getInstance();
        $i->clear();
        $this->assertTrue(count($i->services()) == 0);
    }

    /**
    * auto generate doc.
    */
    public function test_services_register_service(){
        $i = IGKServices::getInstance();
        $i->clear();
        $c = IGKServices::Register(IFooServiceTest::class, DummyFooServiceTest::class);
        $this->assertTrue($c, 'failed to register');
        $tc = $i->services();

        $p = IGKServices::Get(IFooServiceTest::class);
        
        $this->assertTrue($p instanceof DummyFooServiceTest, 'ok service');
        $p->x = 448;
        $g = IGKServices::Get(IFooServiceTest::class);

        $this->assertTrue($p === $g, 'sample not a singleton');  
    }

    /**
    * auto generate doc.
    */
    public function test_services_register_transient_service(){
        $i = IGKServices::getInstance();
        $i->clear();
        $c = IGKServices::Register(IFooServiceTest::class, DummyFooServiceTest::class,null, LifeTime::TRANSIENT);
        $this->assertTrue($c, 'failed to register');        

        $p = IGKServices::Get(IFooServiceTest::class);
        
        $this->assertTrue($p instanceof DummyFooServiceTest, 'ok service');
        $p->x = 448;
        $g = IGKServices::Get(IFooServiceTest::class);

        $this->assertTrue($p !== $g, 'sample a singleton');  
    }
}

/**
* auto generate doc.
* @package IGK\Tests\Core
*/
interface IFooServiceTest extends IAppService{
}

/**
* auto generate doc.
* @package IGK\Tests\Core
*/
class DummyFooServiceTest implements IFooServiceTest{
    use ServicePropertyTrait;

    /**
    * auto generate doc.
    * @var mixed
    */
    var $x = 'value';
}