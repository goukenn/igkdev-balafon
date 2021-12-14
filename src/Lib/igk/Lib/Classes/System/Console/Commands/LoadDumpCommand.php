<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use ZipArchive;

/**
 * restore db from dump json
 */
class LoadDumpCommand extends AppExecCommand{

    var $command = "--db:load_dump";
    var $desc = "Load database dump file";
    var $category = "db";

    public function exec($command, $file=null) { 

        if (empty($file) || !file_exists($file)){
            Logger::danger("Json file required");
            return -1;
        }
        $resetDb =[];
        $filter = [];
        $profil = "";
        if (property_exists($command->options, "-resettables", )){
            $resetDb = explode(",", $command->options->{"-resettables"});
        }

       
        if (property_exists($command->options, "--wordpress")){
            $resetDb = ['wplq_options', "wplq_posts"];
            $filter= [
                "wplq_options"=>"option_value",
                "wplq_posts"=>"post_excerpt|to_ping|pinged|post_content_filtered|post_content|post_title",
                "wplq_term_taxonomy"=>"description"
            ];
            $profil = "wp_"; 
        } 


        $b = json_decode(file_get_contents($file));
        $driver = igk_get_data_adapter(IGK_MYSQL_DATAADAPTER);
        if (!$b){
            Logger::danger("last error: ".json_last_error_msg());
        }else{
          
            $filter_keys = array_keys($filter);
            $driver->setForeignKeyCheck(0);
            foreach($b as $table=>$data){
                if (in_array($table, ["wplq_options"])){
                    continue;
                }

                Logger::info("init : ".$table);
                try{
                    if (in_array( $table, $resetDb )){ 
                        $driver->delete($table); 
                    }
                    $Tcount = 0;
                    $Scount = 0;
                    foreach($data as $row){
                        if (in_array($table, $filter_keys)){
                            foreach(explode("|", $filter[$table]) as $kk){
                                if (!property_exists($row, $kk)){
                                    $row->{$kk} = "";
                                }
                            }
                        }
                        if (method_exists($this, $fc="visit_".$profil.$table)){
                            $this->$fc($row);
                        }
                        if ($driver->insert($table, $row)){
                            $Scount++;
                        }
                        $Tcount++;
                    }
                    Logger::info("Status : {$Scount}/{$Tcount}");
                }catch(\Exception $ex){
                    igk_wln_e("lav::::".$ex->getMessage());
                }
            }
            $driver->setForeignKeyCheck(1);
        }
        Logger::success("finish");

    }

    public function help(){
        parent::help();
        Logger::print(Logger::TabSpace. " [options] file\n");

        Logger::print("Load dump options\n\n");
        Logger::print("--wordpress" . Logger::TabSpace." activate wordpress dumping");
        Logger::print("--resettables".Logger::TabSpace ." reset tables"); 
    }
    private function visit_wp_wplq_posts($row){
        if ($row->post_date_gmt=="0000-00-00 00:00:00"){
            $row->post_date_gmt = $row->post_date;
        }
        if ($row->post_modified_gmt=="0000-00-00 00:00:00"){
            $row->post_modified_gmt = $row->post_date;
        }
    }
    private function visit_wp_wplq_users($row){         
    }
}
