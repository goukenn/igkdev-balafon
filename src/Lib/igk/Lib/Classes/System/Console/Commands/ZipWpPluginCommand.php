<?php
// @author: C.A.D. BONDJE DOUE
// @filename: ZipWpPluginCommand.php
// @date: 20220803 13:48:57
// @desc: 
namespace IGK\System\Console\Commands;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use ZipArchive;

/**
* Zip wp plugin command.
* @package IGK\System\Console\Commands
*/
class ZipWpPluginCommand extends AppExecCommand{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = "--zipwp_plugin";
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = "zip wordpress plugin";
    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $sourcepath
    * @param null|mixed $path
    */
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
        }
        $prjname = basename($sourcepath);
        $fname = "/wp_plugin_.".$prjname.".".date("Ymd").".zip";
        if ($path == null){
            $path = getcwd().$fname;
        } else if (is_dir($path)){
            $path = rtrim($path, "/").$fname;
        }
        $author = $command->app->getConfigs()->get("author", IGK_AUTHOR);
        if (igk_io_file_exists($path)){
            @unlink($path);
        }
        $rgx = "/".$idx."(\/(temp|application))|\.(vscode|git(ignore)?|gkds|DS_Store)$/";
        $zip = new ZipArchive();
        if ($zip->open($path, ZIPARCHIVE::CREATE))
        { 
            igk_zip_dir($sourcepath, $zip,  null, $rgx );
            $manifest = igk_create_xmlnode("manifest");
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