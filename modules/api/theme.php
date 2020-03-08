<?php

/**
 * @Project NUKEVIET 4.x
 * @Author NV Holding <ceo@nvholding.vn>
 * @Copyright (C) 2020 NV Holding. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 07 Mar 2020 09:01:23 GMT
 */

if (!defined('NV_IS_MOD_API'))
    die('Stop!!!');

/**
 * nv_theme_api_main()
 * 
 * @param mixed $array_data
 * @return
 */
function nv_theme_api_main($array_data)
{
    global $module_info, $lang_module, $lang_global, $op;

    $xtpl = new XTemplate($op . '.tpl', NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $module_info['module_theme']);
    $xtpl->assign('LANG', $lang_module);
    $xtpl->assign('GLANG', $lang_global);

    //------------------
    // Viết code vào đây
    //------------------

    $xtpl->parse('main');
    return $xtpl->text('main');
}
