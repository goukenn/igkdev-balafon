<?php

// if (!class_exists('A'))
// {
// class A {
//     function mardi(){
//         return __FUNCTION__;
//     }
// }
// $x = "30"; 
// // jump
// // $y = 45;
// $z = ["zzze R", "data"];
// $tagname = "monaco";

// if ($y == 45)
// {


//     $t["class"] = "google-Roboto";
//     $t->loop($z)->div()->add($tagname)->Content = "My Presentation {{ \$raw | upper }}";
// }   
// $t->div()->Content = "Presentation";
// $t->div()->Content = "%execution_time%";
// }
// $x = 10;
// $y = "118 - ".$x;

// $t->setClass("info google-Roboto");
// $d = igk_create_node("div");
// $d->div()->ul()->loop(range(1, 10))->li()->Content = "data {{ \$raw }}";
// $t->div()->Content = ("Hello ".$x) . $y;
// $t->div()->add($d);

// OK 1
// $t->load('<<?= $x ? >>data</<?= $x ? >>', (object)["transformToEval"=>true]);

$t->loop($x)->div()->Content = "ONE - {{ \$raw }}";
$t->div()->Content = "%execution_time%";
