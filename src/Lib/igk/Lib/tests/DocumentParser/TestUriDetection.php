<?php
// @author: C.A.D. BONDJE DOUE
// @file: TestUriDetection.php
// @date: 20221206 12:28:15
// run: phpunit -c phpunit.xml.dist /Volumes/Data/Dev/PHP/balafon2/src/Lib/igk/Lib/Tests/DocumentParser/TestUriDetection.php
namespace IGK\Tests\DocumentParser;

use igk\devtools\DocumentParser\UriDetector; 
use IGK\System\IO\Path;
use IGK\Tests\BaseTestCase;

///<summary></summary>
/**
* 
* @package IGK\Tests\DocumentParser
*/
class TestUriDetection extends BaseTestCase{

    public static function setUpBeforeClass(): void
    {
        igk_require_module(\igk\devtools::class);
    }

    public function test_ignore_inline_data(){
        $v_detector = new UriDetector;
        $data = "background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e\");";
        $uris = $v_detector->cssUrl($data);        
        $this->assertTrue(is_null($uris), "match uris not ok");
    }
    public function test_detect(){
        $v_detector = new UriDetector;
        $data = "background-image: url(data:/presentation.com);";
        $uris = $v_detector->cssUrl($data);        
        $this->assertTrue(is_null($uris), "match uris not ok");

        $data = "background-image: url(data://presentation.com);";
        $uris = $v_detector->cssUrl($data);        
        $this->assertTrue(!is_null($uris), "match ok");
    }


    public function test_detect_css_cloud(){
        $v_detector = new UriDetector;
        $data = "background-image: url(../webfonts/fa-brands-400.eot?#iefix);";
        $uris = $v_detector->cssUrl($data);        
        $this->assertEquals(
            '../webfonts/fa-brands-400.eot',
            $uris[0]->path
            , "match uris not ok");

 
    }
    public function test_detect_css_svg_data(){
        $v_detector = new UriDetector;
        $data = <<<EOF
background-image : url('data:image/svg+xml;utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="none"%3E%3Ccircle cx="10.5" cy="10.5" r="10.5" fill="%23fff"/%3E%3Cpath fill="%23008A21" fill-rule="evenodd" d="M2.1667 10.5003c0-4.6 3.7333-8.3333 8.3333-8.3333s8.3334 3.7333 8.3334 8.3333S15.1 18.8337 10.5 18.8337s-8.3333-3.7334-8.3333-8.3334zm2.5 0l4.1666 4.1667 7.5001-7.5-1.175-1.1833-6.325 6.325-2.9917-2.9834-1.175 1.175z" clip-rule="evenodd"/%3E%3Cmask id="a" width="17" height="17" x="2" y="2" maskUnits="userSpaceOnUse"%3E%3Cpath fill="%23fff" fill-rule="evenodd" d="M2.1667 10.5003c0-4.6 3.7333-8.3333 8.3333-8.3333s8.3334 3.7333 8.3334 8.3333S15.1 18.8337 10.5 18.8337s-8.3333-3.7334-8.3333-8.3334zm2.5 0l4.1666 4.1667 7.5001-7.5-1.175-1.1833-6.325 6.325-2.9917-2.9834-1.175 1.175z" clip-rule="evenodd"/%3E%3C/mask%3E%3Cg mask="url(%23a)"%3E%3Cpath fill="%23008A21" d="M.5.5h20v20H.5z"/%3E%3C/g%3E%3C/svg%3E');
EOF;
    
        $uris = $v_detector->cssUrl($data);   
        $this->assertNull($uris, "svg response data is not null");

 

    }
    public function test_detect_css_full_uri_data(){
        $v_detector = new UriDetector;
        $data = <<<EOF
background-image :url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/../webfonts/fa-brands-400.svg#fontawesome) format("svg")
EOF;
$uris = $v_detector->cssUrl($data);   
 $path = Path::FlattenPath($uris[0]->path);
        $this->assertEquals(
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/webfonts/fa-brands-400.svg',
            $path, "path flatten"); 

        $rp = $uris[0]->getFlattenReplacement();
        $this->assertEquals(
            'url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/webfonts/fa-brands-400.svg#fontawesome)',
            $rp, "after missting flatten"); 
    }

    public function test_next_css(){
        $v_detector = new UriDetector;
        $data = <<<'CSS'
 div {
    backgroun-color: url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300&display=swap');
 }
.ccueil_bg__EHV6a{background-image:url(/_next/static/media/bg.890002d8.jpg);}
CSS;

        $uris = $v_detector->cssUrl($data);   
        $this->assertFalse(is_null($uris), "must detect uris");
    }

    public function test_uri_on_css(){
        $src = <<<'CSS'
@font-face {
	font-family: 'themify';
	src:url('./fonts/themify.eot?-fvbane');
	src:url('fonts/themify.eot?#iefix-fvbane') format('embedded-opentype'),
		url('fonts/themify.woff?-fvbane') format('woff'),
		url('fonts/themify.ttf?-fvbane') format('truetype'),
		url('fonts/themify.svg?-fvbane#themify') format('svg');
	font-weight: normal;
	font-style: normal;
}

CSS;


$v_detector = new UriDetector;
// igk_debug(1);
$uris = $v_detector->cssUrl($src);

$this->assertEquals(5, count($uris));


    }
}