<?php
// @author: C.A.D. BONDJE DOUE
// @filename: PhpUnitApplication.php
// @date: 20220620 13:53:43
// @desc: Entry unit test application

/**
 * represent the php unit test application
 * @package 
 */
class PhpUnitApplication extends IGKApplicationBase{
    /**
    * Bootstrap.
    */
    public function bootstrap() { 
        $this->library('mysql');
        $this->library('zip');
        $this->library('curl');
        igk_environment()->app_type = 'phpunit';
        igk_server()->REQUEST_URI = "/";
    }
    /**
    * Runs.
    * @param string $entryfile
    * @param mixed $render
    */
    public function run(string $entryfile, $render = 1) {  
        $options = (object)[];
        $argv = igk_getv($_SERVER, 'argv');
        $n = $q = '';
        array_shift($argv); 
        while(count($argv)>0){
            $q = array_shift($argv);
            if (in_array($q, ['-c','--testsuite','--filter']) || preg_match("/^-+/", $q)){
                $n = $q;
                continue;
            }
            if ($n){
                if (isset($options->{$n})){
                    if (!is_array($options->{$n})){
                        $options->{$n} = [$options->{$n}];
                    }
                    $options->{$n}[] = $q;
                } else {
                    $options->{$n} = $q;
                }
            }
        }
        $v_testsuite = igk_getv($options, '--testsuite') ?? [];
        if (is_string($v_testsuite)){
            $v_testsuite = [$v_testsuite];
        }
        $v_test_all_project = in_array('projects', $v_testsuite); 
        $v_test_all_module = in_array('modules', $v_testsuite); 
        igk_server()->prepareServerInfo();
        IGKApp::StartEngine($this);
        $p = igk_sys_project_controllers();        
        if ($p){
            foreach($p as $m){ 
                $m::register_autoload();  
                if ($v_test_all_project){
                    igk_loadlib_dirs($m->getTestClassesDir());
                }
            } 
        }
        if ($tmodule = igk_getv($_ENV,'IGK_TEST_MODULE')){
            $tmodule = igk_require_module($tmodule);
        } 
        if ($m = igk_getv($_ENV,'IGK_TEST_CONTROLER')){
           if ( $m = igk_getctrl($m)){
                $m::register_autoload();
           }
        } 
    }
}