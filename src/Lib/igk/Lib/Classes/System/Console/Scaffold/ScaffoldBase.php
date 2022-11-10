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
  function showHelp($command){ 
  }
  public abstract function exec($command);
}
