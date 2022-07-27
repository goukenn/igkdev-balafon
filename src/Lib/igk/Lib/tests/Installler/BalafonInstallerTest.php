<?php
namespace IGK\Tests\System\Installer;

use Exception;
use IGK\Helper\IO;
use IGK\System\Installers\BalafonInstaller;
use IGK\System\Installers\IMiddleWareAction;
use IGK\System\Installers\InstallerMiddleWareActions;
use IGK\Tests\BaseTestCase;
use IGKException;

class BalafonInstallerTest extends BaseTestCase{
    public function test_dummyIntaller(){
        $this->expectOutputRegex("/unlink/");
        $cwd = getcwd();

        $dir = sys_get_temp_dir()."/installer";
        IO::CreateDir($dir);
        chdir($dir);

        $installer = new MockInstaller();

        $installer->update();


        //restore dir 

        chdir($cwd);
        IO::RmDir($dir);

       // $this->fail("dummy installer");
    }
}


class MockInstaller extends BalafonInstaller{
    public function upload()
    {
        
    }
    public function update()
    {
        $action = new InstallerMiddleWareActions();
        $this->init_installer($action);

        $action->process();
    }
    protected function init_installer(InstallerMiddleWareActions $service)
    {
        $service->add(new Action1MiddelWare());
        $service->add(new Action3MiddelWare());
        $service->add(new Action2MiddelWare());
    }
}
abstract class MockActionBase implements  IMiddleWareAction{
    public function invoke()
    {
        igk_wln("invoke ::: ".static::class);     
        $this->next();
    }
    public function next(){
        if(isset($this->_next)){
            $this->_service->Current++;
            $this->_next->invoke();
        }
    }
    /**
     * call abort 
     * @return mixed 
     * @throws IGKException 
     * @throws Exception 
     */
    public function abort(){
        igk_wln("aborting ::: ".static::class);
        if ($this->_service->Current > 0){
            $this->_service->Current--;
            if ($bserv = igk_getv($this->_service->List, $this->_service->Current)){
                $bserv->abort();
            }
        }
    }
}

class Action1MiddelWare extends MockActionBase{
    public function invoke()
    {
        igk_wln("create file : ");
        igk_io_w2file("sample.txt", "data");
        igk_wln("the file ". realpath("sample.txt"));
        parent::invoke();
    }
    public function abort()
    {    
        igk_wln("unlink ". realpath("sample.txt"));
        @unlink("sample.txt");
        parent::abort();
    }
}
class Action2MiddelWare extends MockActionBase{

}

class Action3MiddelWare extends MockActionBase{
    public function invoke()
    {

        throw new Exception("data - middle 3");
        // $this->abort();
    }
}