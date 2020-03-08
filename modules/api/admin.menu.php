<?php

/**
 * @Project NUKEVIET 4.x
 * @Author NV Holding <ceo@nvholding.vn>
 * @Copyright (C) 2020 NV Holding. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 07 Mar 2020 09:01:23 GMT
 */

if (!defined('NV_ADMIN'))
    die('Stop!!!');

$submenu['config'] = $lang_module['config'];

$allow_func = array();
if (defined('NV_IS_GODADMIN')) {
    $submenu['api-credentials'] = $lang_module['api_cr'];
    $submenu['api-roles'] = $lang_module['api_roles'];
    $submenu['config'] = $lang_module['config'];
    $allow_func[] = 'api-credentials';
    $allow_func[] = 'api-roles';
    $allow_func[] = 'config';
}
