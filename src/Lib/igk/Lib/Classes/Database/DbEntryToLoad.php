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
* auto generate doc.
* @package IGK\Database
*/
final class DbEntryToLoad extends IGKObject{
    var $ctrl, $entries, $tablename;
    public function __construct($ctrl, $tablename, $entries){
        $this->ctrl=$ctrl;
        $this->tablename=$tablename;
        $this->entries=$entries;
    }

    /**
    * auto generate doc.
    */
    public function loadEntries(){
        igk_db_load_entries($this->ctrl, $this->tablename, $this->entries);
    }
}