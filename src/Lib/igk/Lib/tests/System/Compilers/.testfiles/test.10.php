<?php
$x = 999; 

if ($x > 1000){
   $n = igk_create_node("div");
   $n->Content = "greather";
}  
if ($n)
$t->add($n);

igk_do_response($t);
/* 
<?php
$x = '999';
?>
<div><div><?= $x + 10 ?></div><?php if ($x > 10) ?><div>greather</div><?php endif ?> </div>
*/