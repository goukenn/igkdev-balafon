<?php
namespace IGK\System\Console\Commands;

use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use ZipArchive;

class ZipWpPluginCommand extends AppExecCommand{

    var $command = "--zipwp_plugin";

    var $desc = "zip wordpress plugin";


    public function exec($command, $sourcepath=null, $path=null){
       
        if (!extension_loaded("zip") && !function_exists('zip_open')){
            Logger::danger("zip utility function not found");
            return -1;
        }
        if (empty($sourcepath) || !is_dir($sourcepath)){
            
            Logger::danger("source folder not present");
            return false;
        }
        $idx = "";
        if (property_exists($command->options, "--ignore")){
            $ts = $command->options->{"--ignore"};
            if (!is_array($ts))
                $ts = [$ts];
             
            $ts = array_filter($ts);
            $idx = str_replace("/", "\\/", "(".implode("|", $ts).")|");
            // igk_wln_e($command->options->{"--ignore"}, $ts, $idx);
        }
        

        // return;
        $prjname = basename($sourcepath);
        $fname = "/wp_plugin_.".$prjname.".".date("Ymd").".zip";
        if ($path == null){
            $path = getcwd().$fname;
        } else if (is_dir($path)){
            $path = rtrim($path, "/").$fname;
        }
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
       // igk_sys_zip_project($ctrl, $path, $author);

        // $g = $ctrl->getDeclaredDir();
        if (file_exists($path)){
            @unlink($path);
        }
        $rgx = "/".$idx."(\/(temp|application))|\.(vscode|git(ignore)?|gkds|DS_Store)$/";
      //  igk_wln_e($rgx);
        $zip = new ZipArchive();
        if ($zip->open($path, ZIPARCHIVE::CREATE))
        { 
            igk_zip_dir($sourcepath, $zip,  null, $rgx );
            $manifest = igk_createxmlnode("manifest");
            $manifest["xmlns"] = "https://schema.igkdev.com/wp/plugin";
            $manifest["appName"] = $prjname;            
            $manifest->add("author")->Content = $author;
            $manifest->add("date")->Content = date("Ymd His"); 
            $zip->addFromString("manifest.xml", $manifest->render());
            $zip->addFromString("__wp_plugin.def", "<!-- definition -->");
            $zip->close(); 
        }
        Logger::success("zip wp plugin: ".$path);
    }
      
}