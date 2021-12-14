<?php
// @file: IGKDbEntryToLoad.php
// @author: C.A.D. BONDJE DOUE
// @description: 
// @copyright: igkdev © 2021
// @license: Microsoft MIT License. For more information read license.txt
// @company: IGKDEV
// @mail: bondje.doue@igkdev.com
// @url: https://www.igkdev.com

namespace IGK\Database;

use IGKObject;

final class DbEntryToLoad extends IGKObject{
    var $ctrl, $entries, $tablename;
    ///<summary></summary>
    ///<param name="ctrl"></param>
    ///<param name="tablename"></param>
    ///<param name="entries"></param>
    public function __construct($ctrl, $tablename, $entries){
        $this->ctrl=$ctrl;
        $this->tablename=$tablename;
        $this->entries=$entries;
    }
    ///<summary></summary>
    public function loadEntries(){
        igk_db_load_entries($this->ctrl, $this->tablename, $this->entries);
    }
}
