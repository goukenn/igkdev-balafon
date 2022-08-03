<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ServiceTest.php
// @date: 20220803 13:48:54
// @desc: 


use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;

class ServiceTest extends BaseTestCase{
    public function test_service(){
        $srv = igk_app()->getService("ovh");        
        $this->assertEquals(
            null,
            $srv, "service not found"
        );
        IGKServices::Register("ovh", DummyService::class );
        $srv = igk_app()->getService("ovh");        
        $this->assertEquals(
            DummyService::class,
           $srv ? get_class($srv) : null, "service not found"
        );

    }
}

class DummyService implements \IGK\IService{

    public function init(): bool {
        $fc = igk_configs()->get("ovh.ovhconfig");
        if ($fc && file_exists($fc)){
            return true;
        }
        //check if 
        //found the 
        $base = Path::LocalPath(igk_io_basedir());
        while(! ($found = file_exists($fc = $base."/.ovhconfig"))){
            if ($base == ($c=dirname($base))){
                break;
            }
            $base = $c;
        }
        if ($found){
            igk_configs()->{"ovh.ovhconfig"} = $fc;            
            return true;
        }
     }

}