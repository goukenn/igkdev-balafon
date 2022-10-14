<?php
$x = 999;

$t->div()->setContent(igk_php_expression('$x + 10'));

if ($x > 10){
    $t->div()->Content = "greather";
} elseif ($x < 4) {
    $t->div()->panelbox = "finale";
}
/* 
<?php
$x = '09';
?>
<div><div><?= $x + 10 ?></div><?php if ($x > 10) ?><div>greather</div><?php endif ?> </div>
*/