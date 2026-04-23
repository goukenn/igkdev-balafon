<?php

"information " . $x;
$g = 1;
$a = eval('return $g + (function(){ 
    return \'888\';
})();');
echo $a;
exit;
$a + $b;
$t->clearChilds();
if ($data){
    $t->div()->h1()->Content = "Presentation data";
}
foreach($data as $k=>$v){
    $dv =  igk_create_node("div"); 
    $dv->li()->Content = $v;
    $t->add($dv);
}
$t->div()->Content = "OK";