<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_server_static.php
// @date: 20250429 09:37:05
// @desc: serve static resolved resources 

// + | --------------------------------------------------------------------
// + | 
// + |

(function(){
    $v_referer = null;
    if (isset($_SERVER['HTTP_REFERER'])){
        $v_referer = $_SERVER['HTTP_REFERER'];
    }
    $v_uri = $_SERVER['REQUEST_URI'];
    $v_method = $_SERVER['REQUEST_METHOD'];

    $command = (object)[
        'uri'=>$v_uri,
        'method'=>$v_method,
        'referer'=>$v_referer,
    ];

    

})();