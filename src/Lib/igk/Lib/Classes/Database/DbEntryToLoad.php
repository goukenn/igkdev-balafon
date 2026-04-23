<?php
// @file: IGKDbEntryToLoad.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: c.bondje.doue@igkdev.com
// @url: https://www.igkdev.com
namespace IGK\Database;
use IGKObject;

/**
* Db entry to load.
* @package IGK\Database
*/
final class DbEntryToLoad extends IGKObject{
    /**
    * Properties: ctrl, entries, tablename.
    * @var mixed
    */
    var $ctrl, $entries, $tablename;
    /**
    * .ctr
    * @param mixed $ctrl
    * @param mixed $tablename
    * @param mixed $entries
    */
    public function __construct($ctrl, $tablename, $entries){
        $this->ctrl=$ctrl;
        $this->tablename=$tablename;
        $this->entries=$entries;
    }
    /**
    * Loads Entries.
    */
    public function loadEntries(){
        igk_db_load_entries($this->ctrl, $this->tablename, $this->entries);
    }
}