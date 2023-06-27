<?php
// @author: C.A.D. BONDJE DOUE
// @file: RequestCommand.php
// @date: 20221114 01:53:54
namespace IGK\System\Console\Commands;

use IGK\Helper\SysUtils;
use IGK\System\Console\App;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\Console\ServerFakerInput;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGK\System\Html\HtmlContext;
use IGK\System\Uri;
use IGKException;
use ReflectionException;

///<summary></summary>
/**
 * 
 * @package IGK\System\Console\Commands
 */
class RequestViewCommand extends AppExecCommand
{
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
    public function showOptions()
    {
        // + | ----------------------------------
        // + | merge - syntax available for php 8
        // + | ----------------------------------
        $opts = [
            // "--controller:[controller_name]" => "select explicit project controller",
            "--method:[TYPE]" => "request method type. default is GET",
            "--user:[ID]" => "user id to use",
            "--render[:type]" => "render default view, (doc|body|head|view) default is view",
            "--ajx" => "enable ajx render mode",
            "--content-type:[]" => "set render content type. default is 'text/html'",
            "--render-context:[]" => "set rendering context. default is XML",
            "--no-cache" => "disable view cache",
            "+ Server Request COMMAND" => "",
        ];
        $def = DbCommandHelper::GetUsageCommandHelp();
        $opts = array_merge($opts, $def);
        $opts = array_merge($opts, ["+ DB Request COMMAND" => ""]);
        $opts = array_merge($opts, ServerCommandHelper::GetUsageCommandHelp());
        $this->options = $opts;
        parent::showOptions();
    }
    public function showUsage()
    {
        parent::showUsage();
        Logger::print(sprintf(
            "%s controller [request] [options]",
            App::Gets(App::BLUE, $this->command)
        ));
    }
    public function exec($command, $controller = null, ?string $request = null)
    {
        $ctrl = $controller ?? igk_getv($command->options, '--controller');
        if (!$ctrl || !($ctrl = SysUtils::GetControllerByName($ctrl, false))) {
            igk_die('missing controller');
            return -1;
        }
        $path = ltrim(igk_uri($request ?? ''), '/');
        $_SERVER['REQUEST_METHOD'] = igk_getv($command->options, '--method', 'GET');
        $_SERVER['REQUEST_URI'] = '/' . $path; // igk_getv($command->options, '--method', 'GET');
        $_SERVER['HTTP_IGK_AJX'] =  property_exists($command->options, "--ajx");
        $_SERVER['CONTENT_TYPE'] = igk_getv($command->options, "--content-type", "text/html");
        DbCommandHelper::Init($command);
        ServerCommandHelper::Init($command);

        $ctrl->register_autoload();

        if ($id = intval(igk_getv($command->options, '--user'))) {
            self::BindUser($ctrl, $id);
        }
        $render = property_exists($command->options, '--render');


        if ($json = igk_getv($command->options, '--json')) {
            if (file_exists($json)) {
                $json = file_get_contents($json);
                igk_environment()->FakerInput = new ServerFakerInput($json);
            } else {
                $json = null;
            }
        }


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
                case 'view':
                default: 
                    $t->renderAJX($xml_render_option);
                break;
                
            }
            echo "\n";
        }
        error_clear_last();
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
        $g = new Uri($path);
        $path = $g->getPath();
        $_SERVER['REQUEST_URI'] = $g->getRequestUri();
        $_SERVER['QUERY_STRING'] = $g->getQuery();
        igk_server()->prepareServerInfo();
        $ctrl->setCurrentView($path);
    }
}
