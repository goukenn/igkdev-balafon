<?php
namespace Sample;

use \IGK\Actions as action;
use \IGK\Helper\ViewHelper;
use \IGK\BMC\Filters\ButtonFilter;

//%{{# @MainLayout }}

echo ViewHelper::Dir();
$t->clearChilds();
$a = "556";

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