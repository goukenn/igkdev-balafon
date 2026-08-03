<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestCommand.php
// @date: 20221114 01:53:54
namespace IGK\System\Console\Commands;
use IGK\Helper\IO;
use IGK\Helper\SysUtils;
use IGK\Helper\ViewHelper;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\ServerFakerInput;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlContext;
use IGK\System\Http\RequestPreparer;
use IGK\System\Uri;
use IGKException;
use ReflectionException;

/**
* auto generate doc.
* @package IGK\System\Console\Commands
*/
class RequestViewCommand extends AppExecCommand
{
    /**
    * Property: command.
    * @var mixed
    */
    var $command = '--request:view';
    /**
    * Property: desc.
    * @var mixed
    */
    var $desc = 'request view call';
    /**
    * Property: usage.
    * @var mixed
    */
    var $usage = 'controller request [options]';
    /**
    * Shows Options.
    */
    public function showOptions()
    {
        // + | ----------------------------------
        // + | merge - syntax available for php 8
        // + | ----------------------------------
        $opts = [
            "--method:[TYPE]" => "request method type. default is GET",
            "--user:[ID]" => "user id to use",
            "--render:[:type]" => "render default view, (doc|body|head|view|content) default is view",
            "--ajx" => "enable ajx render mode",
            "--json:[file]"=>"file to load as json data",
            "--content-type:[]" => "set render content type. default is 'text/html'",
            "--render-context:[]" => "set rendering context. default is XML",
            "--no-cache" => "disable view cache",
            "--file"=>"file to upload",
            "+ Server Request COMMAND" => "",
        ];
        $def = DbCommandHelper::GetUsageCommandHelp();
        $opts = array_merge($opts, $def);
        $opts = array_merge($opts, ["+ DB Request COMMAND" => ""]);
        $opts = array_merge($opts, ServerCommandHelper::GetUsageCommandHelp());
        $this->options = $opts;
        parent::showOptions();
    }
    /**
    * Exec.
    * @param mixed $command
    * @param null|mixed $controller
    * @param null|string $request
    */
    public function exec($command, $controller = null, ?string $request = null)
    {
        $ctrl = $controller ?? igk_getv($command->options, '--controller');
        if (!$ctrl || !($ctrl = SysUtils::GetControllerByName($ctrl, false))) {
            igk_die('missing controller');
            return -1;
        }
        $files = igk_getv($command->options , '--file');
        $path = ltrim(igk_uri($request ?? ''), '/');
        $_SERVER['REQUEST_METHOD'] = $method = strtoupper(igk_getv($command->options, '--method', 'GET'));
        $_SERVER['REQUEST_URI'] = '/' . $path; 
        $_SERVER['HTTP_IGK_AJX'] = $is_ajx =  property_exists($command->options, "--ajx") || (igk_getv($command->options, "--render") == "ajx");
        $_SERVER['CONTENT_TYPE'] = igk_getv($command->options, "--content-type", "text/html");
        ServerCommandHelper::Init($command);
        $ctrl->register_autoload();
        self::BindUserCommand($ctrl, $command);
        $render = property_exists($command->options, '--render');
        if ($method && $files){
            if (!is_array($files))
                $files = [$files];
            $this->initFiles($files);
        }
        if ($json = igk_getv($command->options, '--json')) {
            if (igk_io_file_exists($json)) {
                $json = file_get_contents($json);
                igk_environment()->FakerInput = new ServerFakerInput($json);
            } else {
                $json = null;
            }
        }  
        igk_server()->REDIRECT_STATUS = 200;
        igk_configs()->default_controller = $ctrl->getName();
        $ctrl->getConfigs()->no_auto_cache_view = property_exists($command->options, '--no-cache');
        $this->doRequest($command, $path);
        if ($render) {
            $v_render_type = igk_getv($command->options, '--render', 'view');
            $doc = $ctrl->getDoc();
            $xml_render_option = (object)[
                "Context" => igk_getv($command->options, '--render-context',  HtmlContext::XML),
                "Indent" => property_exists($command->options, '--indent'),
                "Document"=>$doc,
            ];
            $t = $ctrl->getTargetNode();
            $doc->getBody()->add($t);
            switch ($v_render_type) {
                case 'doc':
                    $doc->renderAJX($xml_render_option);
                    break;
                case 'body':
                    $doc->getBody()->renderAJX($xml_render_option);
                    break;
                case 'head':
                    $doc->getHead()->renderAJX($xml_render_option);
                    break;
                case 'content':
                    echo $t->getInnerHtml($xml_render_option);
                    break;
                case 'view':
                case 'ajx':
                default: 
                    $t->renderAJX($xml_render_option);
                break;
            }
            echo "\n";
        }
        error_clear_last();
    }
    /**
    * auto generate doc.
    * @param array $files
    * @return void
    */
    public function initFiles(array $files){
        $count = 0;
        while(count($files)>0){
            $q = array_shift($files);
            list($file, $name) = igk_extract(explode(';', $q, 2),'0|1');
            $name = $name ?? 'file_'.$count;
            $temp_file = tempnam(sys_get_temp_dir(),'file');
            @unlink($temp_file);
            copy($file, $temp_file);
            $_FILES[$name]= [
                'error'=>0,
                'tmp_name'=>$temp_file,
                'size'=>filesize($file),
                'name'=>basename($file),
                'type'=>IO::MimeTypeFromFile($file)
            ];
            $count++;
        }
    }
    /**
     * do request 
     * @param mixed $command 
     * @param string $path 
     * @return never 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public function doRequest($command, string $path)
    {
        $ctrl = self::GetController(igk_configs()->default_controller, false)
            ?? igk_die("no controller found");
        $path = RequestPreparer::PrepareForRequest($path,  'bcl://request-command.local/');         
        igk_server()->prepareServerInfo();
        list($view, $args) = ViewHelper::PrepareViewArgFromPath($path); 
        if ($args){
            $view .= '/'.implode("/", $args);
            $args = [];
        }
        $ctrl->setCurrentView($view, true, null, $args);
    }
}