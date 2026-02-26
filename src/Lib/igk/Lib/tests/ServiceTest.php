<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ServiceTest.php
// @date: 20220803 13:48:54
// @desc: 

namespace IGK\Tests;

use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;
use IGKServices;
class ServiceTest extends BaseTestCase{

    /**
    * Tests service.
    */
    public function test_service(){
        $service_key = 'test-service';
        $srv = igk_app()->getService($service_key );        
        $this->assertEquals(
            null,
            $srv, 
            "service not found"
        );
        /**
         * register a service 
         */
        IGKServices::Register($service_key , DummyService::class );
        $srv = igk_app()->getService($service_key );        
        $this->assertEquals(
           DummyService::class,
           $srv ? get_class($srv) : null, "service not found"
        );
    }
}

/**
* Dummy service.
* @package IGK\Tests
*/
class DummyService implements \IGK\IService{

    /**
    * Returns Configurable Properties.
    * @return array
    */
    public function getConfigurableProperties(): array { 
        return [];
    }

    /**
    * Initializes.
    * @param null|mixed $options
    * @return bool
    */
    public function init($options =null): bool {
        $fc = igk_configs()->get("ovh.ovhconfig");
        if ($fc && igk_io_file_exists($fc)){
            return true;
        }
        //check if 
        //found the 
        $base = Path::LocalPath(igk_io_basedir());
        while(! ($found = igk_io_file_exists($fc = $base."/.ovhconfig"))){
            if ($base == ($c=dirname($base))){
                break;
            }
            $base = $c;
        }
        if ($found){
            igk_configs()->{"ovh.ovhconfig"} = $fc;            
            return true;
        }
        return false;
     }

}