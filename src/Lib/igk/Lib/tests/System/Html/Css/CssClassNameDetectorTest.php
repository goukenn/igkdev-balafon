<?php
// @author: C.A.D. BONDJE DOUE
// @file: CssClassNameDetectorTest.php
// @date: 20240913 09:35:04
namespace IGK\Tests\System\Html\Css;

use IGK\System\Html\Css\CssClassNameDetector;
use IGK\System\Html\Css\CssClassNameDetectorUtils;
use IGK\System\Html\Css\CssParser;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\System\Html\Css
* @author C.A.D. BONDJE DOUE
*/
class CssClassNameDetectorTest extends BaseTestCase{
    public function test_cssclassdetector_detect_class(){
        $parser = CssParser::Parse(".card{ display:block; }");
        $detector = new CssClassNameDetector;
        $detector->map($parser->to_array());
        $arr = $detector->resolv("info card");
        $this->assertTrue(is_array($arr)); 

        $this->assertEquals(".card{display:block;}",
        $detector->renderToCss($arr, (object)["lf"=>""]));
    }

    public function test_cssclassdetector_detect_class_php(){
        // parse css content
        $parser = CssParser::Parse(".card{ display:block; } div.container{ width:auto;} div.info{height:3em;} @media (max-width:420px){ div.info{  } .card{ display: flex;}}");
        // load to class detector 
        $detector = new CssClassNameDetector;
        $detector->map($parser->to_array());
        $arr = CssClassNameDetectorUtils::DetectFromPhpSource($detector, <<<'PHP'
<?php
$a = "presentation info";
$b = "<html className=\"card\"></html>";
?><div class="info">for information</div>
PHP
);

        // $arr = $detector->resolv("info card");
        $this->assertTrue(is_array($arr)); 

        $this->assertEquals(".card{display:block;}div.info{height:3em;}@media (max-width:420px){.card{display:flex;}}",
        $detector->renderToCss($arr, (object)["lf"=>""]));
    }
}