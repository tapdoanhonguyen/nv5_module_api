<?php

/**
 * @Project NUKEVIET 4.x
 * @Author NV Holding <ceo@nvholding.vn>
 * @Copyright (C) 2020 NV Holding. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 07 Mar 2020 09:01:23 GMT
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN'))
    die('Stop!!!');

define('NV_IS_FILE_API', true);
$nv_Lang = new \NukeViet\Core\Language();
$nv_Lang->loadGlobal();
/* $allow_func = array('main', 'config');
 */
 
 function nv_get_api_actions()
{
    global $nv_Lang, $sys_mods;

    $array_apis = [
        '' => []
    ];
    $array_keys = $array_cats = $array_apis;
	$api_not_access = array('Api','IApi', 'Exception','ApiResult');
    // Các API của hệ thống
    $files = nv_scandir(NV_ROOTDIR . '/vendor/vinades/nukeviet/Api', '/(.*?)/');
    foreach ($files as $file) {
        if (preg_match('/^([^0-9]+[a-z0-9\_]{0,})\.php$/', $file, $m)) {
            $class_name = $m[1];
			if(!in_array($class_name,$api_not_access,true)){
				$class_namespaces = 'NukeViet\\Api\\' . $class_name;
				if (nv_class_exists($class_namespaces)) {
					$class_cat = $class_namespaces::getCat();
					$cat_title = $nv_Lang->getModule('api_' . $class_cat);
					$api_title = $nv_Lang->getModule('api_' . $class_cat . '_' . $class_name);
					if (!isset($array_apis[''][$class_cat])) {
						$array_apis[''][$class_cat] = [
							'title' => $nv_Lang->getModule('api_' . $class_cat),
							'apis' => []
						];
					}
					$array_apis[''][$class_cat]['apis'][$class_name] = [
						'title' => $api_title,
						'cmd' => $class_name
					];
					$array_keys[''][$class_name] = $class_name;
					$array_cats[''][$class_name] = [
						'key' => $class_cat,
						'title' => $cat_title,
						'api_title' => $api_title
					];
				}
			}
        }
    }

    // Các API của module cung cấp
    foreach ($sys_mods as $module_name => $module_info) {
        $module_file = ucfirst($module_info['module_file']);
        if (file_exists(NV_ROOTDIR . '/vendor/vinades/nukeviet/Module/' . $module_file . '/Api')) {
            // Đọc ngôn ngữ tạm của module
            $nv_Lang->loadModule($module_file, false, true);

            // Lấy các API
            $files = nv_scandir(NV_ROOTDIR . '/vendor/vinades/nukeviet/Module/' . $module_file . '/Api', '/(.*?)/');
            foreach ($files as $file) {
                if (preg_match('/^([^0-9]+[a-z0-9\_]{0,})\.php$/', $file, $m)) {
                    $class_name = $m[1];
                    $class_namespaces = 'NukeViet\\Module\\' . $module_file . '\\Api\\' . $class_name;
                    if (nv_class_exists($class_namespaces)) {
                        $class_cat = $class_namespaces::getCat();
                        $cat_title = $class_cat ? $nv_Lang->getModule('api_' . $class_cat) : '';
                        $api_title = $class_cat ? $nv_Lang->getModule('api_' . $class_cat . '_' . $class_name) : $nv_Lang->getModule('api_' . $class_name);

                        // Xác định key
                        if (!isset($array_keys[$module_name])) {
                            $array_keys[$module_name] = [];
                        }
                        $array_keys[$module_name][$class_name] = $class_name;

                        // Xác định cây thư mục
                        if (!isset($array_apis[$module_name])) {
                            $array_apis[$module_name] = [];
                        }
                        if (!isset($array_apis[$module_name][$class_cat])) {
                            $array_apis[$module_name][$class_cat] = [
                                'title' => $cat_title,
                                'apis' => []
                            ];
                        }
                        $array_apis[$module_name][$class_cat]['apis'][$class_name] = [
                            'title' => $api_title,
                            'cmd' => $class_name
                        ];

                        // Phân theo cat
                        if (!isset($array_cats[$module_name])) {
                            $array_cats[$module_name] = [];
                        }
                        $array_cats[$module_name][$class_name] = [
                            'key' => $class_cat,
                            'title' => $cat_title,
                            'api_title' => $api_title
                        ];
                    }
                }
            }

            // Xóa ngôn ngữ tạm
            $nv_Lang->changeLang();
        }
    }

    return [$array_apis, $array_keys, $array_cats];
}