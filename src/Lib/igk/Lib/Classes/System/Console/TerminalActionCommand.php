<?php
// @author: C.A.D. BONDJE DOUE
// @filename: TerminalActionCommand.php
// @date: 20220421 08:02:53
// @desc: Terminal action command


namespace IGK\System\Console;

use Error;

// terminal action command

/**
 * terminal action command
 * @package IGK\System\Console
 */
class TerminalActionCommand{
    /**
     * the primary command promp^t
     * @var string
     */
    protected $prompt = "\$tac>";

    /**
     * store running command
     * @var array
     */
    protected $commands = [];
    public function run(){
        $result = 0;
         
        do{
            $command = readline($this->prompt);
            $e = $command != "quit";
            if ($e && !empty($command)){ 
                array_unshift($this->commands, $command);
                readline_add_history($command);
                try{
                    // if (($f = igk_getv(explode(" ", $command), 0))
                    //     && method_exists($this, $f)){
                    
                    // }

                    var_dump(@eval("return ".$command.";"));
                }
                catch(Error $e){
                    Logger::danger("error append : ".$e->getMessage());
                }
            }  
        }
        while($e);
        return $result;
    }
}