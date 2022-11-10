<?php
"information " . $x;



//$a = $b + $c;
// -> $___IGK_PHP__[] = eval_express('b') + eval_express('c');
// -> $___IGK_PHP__[] = express_eval_('$a + $b');
$g = 1;
$a = eval('return $g + (function(){ 
    return \'888\';
})();');

echo $a;
exit;
$a + $b;
// -> $___IGK_PHP__[] = express_eval_('$a + $b');
// echo ViewHelper::Dir();
// $t->clearChilds();
// $a = "556";
//#{{ @Import 'test.pinc'}}

$t->clearChilds();

if ($data){
    //%{{ @Import 'test.pinc'}}
    $t->div()->h1()->Content = "Presentation data";
}

foreach($data as $k=>$v){
    $dv =  igk_create_node("div"); // $t->div();
    $dv->li()->Content = $v;
    $t->add($dv);
}

$t->div()->Content = "OK";

// $___IGK_PHP_SETTER___['a'] = igk_create_node("div");// $t->clearChilds()->bodybox();
// $___IGK_PHP_SETTER___['a']->p()->Content = "Sample";
// $___IGK_PHP_SETTER___['a']->li()->Content = "the li";
// $___IGK_PHP_SETTER___['x'] = 'data-'.(80);
// // igk_wln_e("the setter : === ", $a ); // $___IGK_PHP_SETTER___['a']);
// // $__IGK_PHP_SETTER__['a']->Content = "sample a";
// $t->add($a)->p()->Content = $x;
// $t->div()->Content = "OK";

// interface JOGO{
//     function base();
// }

// if (!class_exists("Domino")){
//     class Domino{
//         var $x;
//         var $y;
//     }
//     $t->div()->setClass("sample inblock")->Content = "In block-----";
// }
// $t->div()->Content = "Marco";
// class MyClass{
// }
// trait MyTrait{
//     var $x= 8;
// }
// $t->p()->Content = "POLO";