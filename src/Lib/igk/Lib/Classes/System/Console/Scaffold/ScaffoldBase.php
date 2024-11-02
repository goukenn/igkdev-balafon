<?php

// @author: C.A.D. BONDJE DOUE
// @filename: ScaffoldBase.php
// @date: 20220622 20:47:02
// @desc: 


namespace IGK\System\Console\Scaffold;

/**
 * scaffold command
 * @package IGK\System\Console\Scaffold
 */
abstract class ScaffoldBase{
  var $description;
  /**
   * sho help 
   * @param mixed $command 
   * @return mixed 
   */
  abstract function showHelp($command);
  /**
   * help command option 
   * @param mixed $command 
   * @return mixed 
   */
  public abstract function exec($command);
}
