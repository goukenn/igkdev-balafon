<?php
// @author: C.A.D. BONDJE DOUE
// @file: DarshboardKPI.php
// @date: 20260819 10:25:07
namespace IGK\System\WinUI\Dashboard;


/**
* KPI used to show global dashboard view Widget 
* @package IGK\System\WinUI\Dashboard
* @author C.A.D. BONDJE DOUE
*/
class DashboardKPI{
    /**
     * name of the kpi
     * @var mixed
     */
    var $name;
    /**
     * title label
     * @var ?string
     */
    var $label;
    /**
     * identifier of the kpi 
     * @var mixed
     */
    var $key;
    /**
     * 
     * @var mixed
     */
    var $value;
    /**
     * 
     * @var mixed
     */
    var $sub;
    /**
     * ion that will identifier the kpi 
     * @var mixed
     */
    var $icon;
    /**
     * 
     * @var mixed
     */
    var $order;

    /**
     * authorisation to view the kpi
     * @var mixed
     */
    var $auth;
}