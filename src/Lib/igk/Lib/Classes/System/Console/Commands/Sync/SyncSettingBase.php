<?php

// @author: C.A.D. BONDJE DOUE
// @filename: SyncSettingBase.php
// @date: 20220518 09:57:10
// @desc: 

namespace IGK\System\Console\Commands\Sync;

abstract class SyncSettingBase{
    /**
     * installing core 
     * @var string
     */
    var $core;

    /**
     * ftp server
     * @var string
     */
    var $server;

    /**
     * ftp user login
     * @var string
     */
    var $user;

    /**
     * ftp password
     * @var string
     */
    var $password;

    /**
     * where to store project releases
     * @var string
     */
    var $release;


}
