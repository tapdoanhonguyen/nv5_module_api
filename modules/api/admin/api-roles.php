<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2020 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sun, 22 Mar 2020 05:07:33 GMT
 */

if (!defined('NV_IS_FILE_API'))
    die('Stop!!!');
$array_api_actions = nv_get_api_actions();
$array_api_cats = $array_api_actions[2];
$array_api_keys = $array_api_actions[1];
$array_api_actions = $array_api_actions[0];
$current_cat = '';
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $role_id = $nv_Request->get_int('role_id', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $role_id;
    if ($new_vid > 0)     {
        $sql = 'SELECT role_id FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_role WHERE role_id!=' . $role_id . ' ORDER BY addtime ASC';
        $result = $db->query($sql);
        $addtime = 0;
        while ($row = $result->fetch())
        {
            ++$addtime;
            if ($addtime == $new_vid) ++$addtime;             $sql = 'UPDATE ' . NV_AUTHORS_GLOBALTABLE . '_api_role SET addtime=' . $addtime . ' WHERE role_id=' . $row['role_id'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . NV_AUTHORS_GLOBALTABLE . '_api_role SET addtime=' . $new_vid . ' WHERE role_id=' . $role_id;
        $db->query($sql);
        $content = 'OK_' . $role_id;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('delete_role_id', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $role_id = $nv_Request->get_int('delete_role_id', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($role_id > 0 and $delete_checkss == md5($role_id . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $addtime=0;
        $sql = 'SELECT addtime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_role WHERE role_id =' . $db->quote($role_id);
        $result = $db->query($sql);
        list($addtime) = $result->fetch(3);
        
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_role  WHERE role_id = ' . $db->quote($role_id));
        if ($addtime > 0)         {
            $sql = 'SELECT role_id, addtime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_role WHERE addtime >' . $addtime;
            $result = $db->query($sql);
            while (list($role_id, $addtime) = $result->fetch(3))
            {
                $addtime--;
                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_role SET addtime=' . $addtime . ' WHERE role_id=' . intval($role_id));
            }
        }
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Role', 'ID: ' . $role_id, $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();
$row['role_id'] = $nv_Request->get_int('role_id', 'post,get', 0);
$role_id = $nv_Request->get_int('role_id', 'get', 0);

if ($role_id) {
    if (!isset($array[$role_id])) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;role_id=' . $role_id;
    $array_post = $array[$role_id];
    if (!isset($array_post['role_data']['sys'])) {
        $array_post['role_data']['sys'] = [];
    }
    if (!isset($array_post['role_data'][NV_LANG_DATA])) {
        $array_post['role_data'][NV_LANG_DATA] = [];
    }
} else {
    $form_action = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    $array_post = array(
        'role_title' => '',
        'role_description' => ''
    );
    $array_post['role_data'] = [];
    $array_post['role_data']['sys'] = [];
    $array_post['role_data'][NV_LANG_DATA] = [];
}

if ($nv_Request->isset_request('submit', 'post')) {
	$current_cat = $nv_Request->get_title('current_cat', 'post', '');
    $row['role_title'] = $nv_Request->get_title('role_title', 'post', '');
    $row['role_description'] = $nv_Request->get_textarea('role_description', '', NV_ALLOWED_HTML_TAGS);
	$row['role_data'] = [];
    // Các API của hệ thống
    $row['role_data']['sys'] = [];
    // Các API của module theo ngôn ngữ
    $row['role_data'][NV_LANG_DATA] = [];

    // Lấy các API được phép
    foreach ($array_api_actions as $keysysmodule => $sysmodule_data) {
        // Các API không có CAT
        $api_nocat = $nv_Request->get_typed_array('api_' . $keysysmodule, 'post', 'string', []);
        $api_cat = [];
        foreach ($sysmodule_data as $catkey => $catapis) {
            $api_cat = array_merge_recursive($api_cat, $nv_Request->get_typed_array('api_' . $keysysmodule . '_' . $catkey, 'post', 'string', []));
        }
        $api_submits = array_filter(array_unique(array_merge_recursive($api_nocat, $api_cat)));
        $api_submits = array_intersect($api_submits, $array_api_keys[$keysysmodule]);
        if (empty($keysysmodule)) {
            $row['role_data']['sys'] = $api_submits;
        } elseif (!empty($api_submits)) {
            $row['role_data'][NV_LANG_DATA][$keysysmodule] = $api_submits;
        }
    }

    $sql = 'SELECT role_id FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_role WHERE role_title=:role_title' . ($role_id ? (' AND role_id!=' . $role_id) : '');
    $sth = $db->prepare($sql);
    $sth->bindParam(':role_title', $row['role_title'], PDO::PARAM_STR);
    $sth->execute();
    $is_exists = $sth->fetchColumn();

    if (empty($row['role_title'])) {
        $error[] = $lang_module['error_required_role_title'];
    } /* elseif (empty($row['role_data'])) {
        $error[] = $lang_module['error_required_role_data'];
    } */

    if (empty($error)) {
        try {
			$content_role_data = serialize($row['role_data']);print_r($content_role_data);
            if (empty($row['role_id'])) {
                $row['edittime'] = 0;

                $stmt = $db->prepare('INSERT INTO ' . NV_AUTHORS_GLOBALTABLE . '_api_role (role_title, role_description, role_data, addtime, edittime) VALUES (:role_title, :role_description, :role_data, :addtime, :edittime)');

                $weight = $db->query('SELECT max(addtime) FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_role')->fetchColumn();
                $weight = intval($weight) + 1;
                $stmt->bindParam(':addtime', $weight, PDO::PARAM_INT);

                $stmt->bindParam(':edittime', $row['edittime'], PDO::PARAM_INT);

            } else {
                $stmt = $db->prepare('UPDATE ' . NV_AUTHORS_GLOBALTABLE . '_api_role SET role_title = :role_title, role_description = :role_description, role_data = :role_data WHERE role_id=' . $row['role_id']);
            }
            $stmt->bindParam(':role_title', $row['role_title'], PDO::PARAM_STR);
            $stmt->bindParam(':role_description', $row['role_description'], PDO::PARAM_STR, strlen($row['role_description']));
            $stmt->bindParam(':role_data', $content_role_data, PDO::PARAM_STR, strlen($content_role_data));

            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                if (empty($row['role_id'])) {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Add Role', ' ', $admin_info['userid']);
                } else {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit Role', 'ID: ' . $row['role_id'], $admin_info['userid']);
                }
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
            }
        } catch(PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); //Remove this line after checks finished
        }
    }
} elseif ($row['role_id'] > 0) {
    $row = $db->query('SELECT * FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_role WHERE role_id=' . $row['role_id'])->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
} else {
    $row['role_id'] = 0;
    $row['role_title'] = '';
    $row['role_description'] = '';
    $row['role_data'] = '';
}

$row['role_description'] = nv_htmlspecialchars(nv_br2nl($row['role_description']));
$row['role_data'] = nv_htmlspecialchars(nv_br2nl($row['role_data']));


$q = $nv_Request->get_title('q', 'post,get');

// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . NV_AUTHORS_GLOBALTABLE . '_api_role');

    if (!empty($q)) {
        $db->where('role_title LIKE :q_role_title OR role_data LIKE :q_role_data');
    }
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_role_title', '%' . $q . '%');
        $sth->bindValue(':q_role_data', '%' . $q . '%');
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('addtime ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_role_title', '%' . $q . '%');
        $sth->bindValue(':q_role_data', '%' . $q . '%');
    }
    $sth->execute();
}
$cat_order = 0;
$total_api_enabled = 0;
$array_api_trees = [];
$array_api_contents = [];

foreach ($array_api_actions as $keysysmodule => $sysmodule_data) {
    $cat1_is_active = ($keysysmodule == $current_cat and !empty($current_cat)) ? true : false;
    $cat1_total_api = 0;

    // Lev1: Hệ thống hoặc các module
    $array_api_trees[$keysysmodule] = [
        'active' => $cat1_is_active,
        'total_api' => 0,
        'key' => $keysysmodule,
        'name' => $keysysmodule ? $site_mods[$keysysmodule]['custom_title'] : $nv_Lang->getModule('api_of_system'),
        'subs' => []
    ];

    // Lev 2: Các cat của hệ thống hoặc các module, trong HTML đối xử ngang nhau
    foreach ($sysmodule_data as $catkey => $catapis) {
        $cat_order++;

        if (!empty($catkey)) {
            $cat2_key = $keysysmodule . '_' . $catkey;
            $cat2_is_active = ($cat2_key == $current_cat or (!$cat1_is_active and $cat_order == 1 and empty($current_cat))) ? true : false;
            $cat2_total_api = 0;

            $array_api_trees[$keysysmodule]['subs'][$cat2_key] = [
                'active' => $cat2_is_active,
                'total_api' => 0,
                'key' => $cat2_key,
                'name' => $catapis['title']
            ];

            // Các API của lev1 (Các api có cat của lev2 trống)
            $array_api_contents[$cat2_key] = [
                'key' => $cat2_key,
                'active' => $cat2_is_active,
                'apis' => []
            ];

            foreach ($catapis['apis'] as $api) {
                $api_checked = ((empty($keysysmodule) and in_array($api['cmd'], $array_post['role_data']['sys'])) or (!empty($keysysmodule) and isset($array_post['role_data'][NV_LANG_DATA][$keysysmodule]) and in_array($api['cmd'], $array_post['role_data'][NV_LANG_DATA][$keysysmodule])));
                $total_api_enabled += $api_checked ? 1 : 0;
                $cat2_total_api += $api_checked ? 1 : 0;

                $array_api_contents[$cat2_key]['apis'][] = [
                    'cmd' => $api['cmd'],
                    'name' => $api['title'],
                    'checked' => $api_checked
                ];
            }

            $array_api_trees[$keysysmodule]['subs'][$cat2_key]['total_api'] = $cat2_total_api;
        } else {
            // Các API của lev1 (Các api có cat của lev2 trống)
            $array_api_contents[$keysysmodule] = [
                'key' => $keysysmodule,
                'active' => $cat1_is_active,
                'apis' => []
            ];

            foreach ($catapis['apis'] as $api) {
                $api_checked = ((empty($keysysmodule) and in_array($api['cmd'], $array_post['role_data']['sys'])) or (!empty($keysysmodule) and isset($array_post['role_data'][NV_LANG_DATA][$keysysmodule]) and in_array($api['cmd'], $array_post['role_data'][NV_LANG_DATA][$keysysmodule])));
                $total_api_enabled += $api_checked ? 1 : 0;
                $cat1_total_api += $api_checked ? 1 : 0;

                $array_api_contents[$keysysmodule]['apis'][] = [
                    'cmd' => $api['cmd'],
                    'name' => $api['title'],
                    'checked' => $api_checked
                ];
            }
        }
    }

    $array_api_trees[$keysysmodule]['total_api'] = $cat1_total_api;
}
$xtpl = new XTemplate('api-roles.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$xtpl->assign('NV_LANG_DATA', NV_LANG_DATA);
$xtpl->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$xtpl->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$xtpl->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('MODULE_UPLOAD', $module_upload);
$xtpl->assign('NV_ASSETS_DIR', NV_ASSETS_DIR);
$xtpl->assign('OP', $op);
$xtpl->assign('ROW', $row);

$xtpl->assign('Q', $q);

if ($show_view) {
    $base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
    if (!empty($q)) {
        $base_url .= '&q=' . $q;
    }
    $generate_page = nv_generate_page($base_url, $num_items, $per_page, $page);
    if (!empty($generate_page)) {
        $xtpl->assign('NV_GENERATE_PAGE', $generate_page);
        $xtpl->parse('main.view.generate_page');
    }
    $number = $page > 1 ? ($per_page * ($page - 1)) + 1 : 1;
    while ($view = $sth->fetch()) {
        for($i = 1; $i <= $num_items; ++$i) {
            $xtpl->assign('WEIGHT', array(
                'key' => $i,
                'title' => $i,
                'selected' => ($i == $view['addtime']) ? ' selected="selected"' : ''));
            $xtpl->parse('main.view.loop.addtime_loop');
        }
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;role_id=' . $view['role_id'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_role_id=' . $view['role_id'] . '&amp;delete_checkss=' . md5($view['role_id'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}
foreach ($array_api_trees as $apilev1){
	if($apilev1['active']){
		$apilev1_active = 'class="active"';
	}else{
		$apilev1_active = "";
	}
	$xtpl->assign('ACTIVE', $apilev1_active);
	$xtpl->assign('apilev1', $apilev1);
	foreach($apilev1['subs'] as $apilev2){
		if($apilev2['active']){
			$apilev2_active = 'class="active"';
		}else{
			$apilev2_active = "";
		}
		$xtpl->assign('ACTIVESUB', $apilev2_active);
		$xtpl->assign('apilev2', $apilev2);
		$xtpl->parse('main.cat_module.sub_cat');
	}
	$xtpl->parse('main.cat_module');
}
foreach ($array_api_contents as $apicontent){
	if($apicontent['active']){
		$apicontent_active = 'style="display: block;"';
	}else{
		$apicontent_active = 'style="display: none;"';
	}
	$xtpl->assign('ACTIVECONTENT', $apicontent_active);
	$xtpl->assign('apicontent', $apicontent);
	foreach($apicontent['apis'] as $api){
		$xtpl->assign('api', $api);
		$xtpl->parse('main.tab_module.api');
	}
	$xtpl->parse('main.tab_module');
}

if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['role'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
