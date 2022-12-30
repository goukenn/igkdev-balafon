<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestCommand.php
// @date: 20221114 01:53:54
namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Html\HtmlContext;

///<summary></summary>
/**
* 
* @package IGK\System\Console\Commands
*/
class RequestViewCommand extends AppExecCommand{
    var $command = '--request:view';

    var $desc = 'request view call';

    // var $options = [
    //     "+ Server Request COMMAND"=>"",
    //     ...DbCommandHelper::GetUsageCommandHelp(),
    //     "+ DB Request COMMAND"=>"",
    //     ...ServerCommandHelper::GetUsageCommandHelp(),
    //     "--method:[TYPE]"=>"request method type. default is GET",
    //     "--user:[ID]"=>"user id to use",
    //     "--render:[ID]"=>"render default view",        
    // ];
    public function showOptions(){

        // syntax available for php 8
        $opts = [
            "--method:[TYPE]"=>"request method type. default is GET",
            "--user:[ID]"=>"user id to use",
            "--render"=>"render default view",        
            "--ajx"=>"enable ajx render mode",        
            "--content-type:[]"=>"set render content type. default is 'text/html'",        
            "--render-context:[]"=>"set rendering context. default is XML",        
            "+ Server Request COMMAND"=>"",
        ];
        $def = DbCommandHelper::GetUsageCommandHelp();
        $opts = array_merge($opts, $def);
        $opts = array_merge($opts, ["+ DB Request COMMAND"=>""]);
        $opts = array_merge($opts,ServerCommandHelper::GetUsageCommandHelp());
        $this->options = $opts;
        parent::showOptions();
    }
    public function showUsage(){
        parent::showUsage();
        Logger::print(sprintf("%s controller [request] [options]", 
            App::Gets(App::BLUE, $this->command)
        ));
    }
    public function exec($command, $controller = null, ?string $request=null) { 
        if (! ($ctrl = SysUtils::GetControllerByName($controller, false))){
            igk_die('missing controller');
            return -1;
        }
        $path = ltrim(igk_uri($request ?? ''), '/');
        $_SERVER['REQUEST_METHOD'] = igk_getv($command->options, '--method', 'GET');
        $_SERVER['REQUEST_URI'] = '/'.$path; // igk_getv($command->options, '--method', 'GET');
        $_SERVER['HTTP_IGK_AJX'] =  property_exists($command->options, "--ajx");
        $_SERVER['CONTENT_TYPE'] = igk_getv($command->options, "--content-type", "text/html");
        DbCommandHelper::Init($command);
        ServerCommandHelper::Init($command);
        if ($id = intval(igk_getv($command->options, '--user'))){
            if ($user = \IGK\Models\Users::Get('clId', $id)){
                $ctrl::login($user, null, false);
            }
        }
        $render = property_exists($command->options, '--render');
        if (!$render){
            Logger::print("request: ".$path);
            Logger::print("method : ".igk_server()->REQUEST_METHOD);
        }
        else {
            Logger::print("Content-Type: ".igk_server()->CONTENT_TYPE);
            Logger::print("\n");
        }
        igk_configs()->default_controller = $ctrl->getName();
        $ctrl->setConfig('no_auto_cache_view', property_exists($command->options, '--no-cache'));
        $this->doRequest($command, $path);
      

        if ($render){ 
            $xml_render_option = (object)[
                "Context"=>igk_getv($command->options, '--render-context',  HtmlContext::XML),
                "Indent"=>property_exists($command->options, '--indent'),
            ];
            $ctrl->getTargetNode()->renderAJX($xml_render_option);
            echo "\n";
        } 
        error_clear_last();
        // igk_dev_wln(__FILE__.":".__LINE__);
        // Logger::info('done');
    }
    public function doRequest($command, $path){
        $ctrl = self::GetController(igk_configs()->default_controller, false)
        ?? igk_die("no controller found");
        
        $ctrl->setCurrentView($path);
    }
}