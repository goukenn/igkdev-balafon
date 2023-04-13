<?php
// @author: C.A.D. BONDJE DOUE
// @filename: igk_redirection.php
// @date: 20230413 12:42:49
// @desc: handle redirection 

if (defined("IGK_REDIRECTION") && (IGK_REDIRECTION == 1))
    return;
define('IGK_REDIRECTION', 1);
if (!isset($redirect)) {
    require_once __DIR__ . '/igk_framework.php';
    $server_info = (object)array();
    foreach (array(
        "REQUEST_URI" => '',
        "SERVER_PROTOCOL" => '',
        "REDIRECT_STATUS" => '',
        "REDIRECT_URL" => '',
        "REDIRECT_REQUEST_METHOD" => 'GET',
        "REDIRECT_QUERY_STRING" => ''
    ) as $k => $v) {
        $server_info->$k = igk_getv($_SERVER, $k, $v);
    }
    $redirect = $server_info->{'REQUEST_URI'};
}
igk_io_handle_system_command($redirect);
