<?php
// @author: C.A.D. BONDJE DOUE
// @filename: WebInitComponentInsterfaceCommand.php
// @date: 20220803 13:48:57
// @desc: 


namespace IGK\System\Console\Commands;

use IGK\Helper\PhpHelper;
use IGK\System\Console\AppExecCommand;
use IGK\System\Console\Logger;
use IGK\System\IO\File\PHPScriptBuilder; 
use ReflectionFunction;

// class WebInitComponentInsterfaceCommand extends AppExecCommand
// {
//     var $command = "--web:init-component";
//     var $desc = "init component file interface helper";
//     var $category = "web";

//     public function exec($command)
//     { 
//         $doc = new \IGK\System\IO\File\Php\PhpInterfaceDocument([PhpHelper::class, "HtmlComponentDocumention"]);
//         $doc->name = "IHtmlComponent";
//         $doc->type = "interface";  
//         $doc->doc = "Html - core components";   
//         $file = IGK_LIB_CLASSES_DIR . "/System/Html/Dom/IHtmlComponent.php";        
//         $s = $doc->generate();        
//         igk_io_w2file($file, $s);    
//         Logger::success("done");
//     }

// }
