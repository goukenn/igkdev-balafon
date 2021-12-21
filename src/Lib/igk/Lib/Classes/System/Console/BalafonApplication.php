<?php
namespace IGK\System\Console;

use IGKApp;
use IGKApplicationBase;

/** @package  */
class BalafonApplication extends IGKApplicationBase{
    public $command;

    public function run(string $entryfile, $render = 1) { 
        IGKApp::StartEngine($this);
        return \IGK\System\Console\App::Run($this->command);
    }

}