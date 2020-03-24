<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2020 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 23 Mar 2020 12:27:13 GMT
 */

if (!defined('NV_IS_FILE_API'))
    die('Stop!!!');



if ($nv_Request->isset_request('delete_credential_ident', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $credential_ident = $nv_Request->get_string('delete_credential_ident', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($credential_ident != '' and $delete_checkss == md5($credential_ident . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $db->query('DELETE FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_credential  WHERE credential_ident = ' . $db->quote($credential_ident));
        
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Credential', 'ID: ' . $credential_ident, $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();
$row['credential_ident'] = $nv_Request->get_string('credential_ident', 'post,get', '');
if ($nv_Request->isset_request('submit', 'post')) {
    $row['admin_id'] = $nv_Request->get_int('admin_id', 'post', 0);
    $row['credential_title'] = $nv_Request->get_title('credential_title', 'post', '');
	$array_post['credential_title'] = nv_substr($nv_Request->get_title('credential_title', 'post', ''), 0, 255);
        if (empty($credential_ident)) {
            $array_post['admin_id'] = $nv_Request->get_int('admin_id', 'post', 0);
        }
    $_api_roles = $nv_Request->get_array('api_roles', 'post');
    $row['api_roles'] = !empty($_api_roles) ? implode(',', $_api_roles) : '';

    if (empty($row['credential_title'])) {
        $error[] = $lang_module['error_required_credential_title'];
    }

    if (empty($error)) {
        try {
			 $new_credential_ident = '';
			$new_credential_secret = '';
			while (empty($new_credential_ident) or $db->query('SELECT admin_id FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_credential WHERE credential_ident=' . $db->quote($new_credential_ident))->fetchColumn()) {
				$new_credential_ident = nv_genpass(32, 3);
			}
			while (empty($new_credential_secret) or $db->query('SELECT admin_id FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_credential WHERE credential_ident=' . $db->quote($new_credential_secret))->fetchColumn()) {
				$new_credential_secret = nv_genpass(32, 3);
			}
			 $new_credential_secret_db = $crypt->encrypt($new_credential_secret);
            if (empty($row['credential_ident'])) {
                $row['last_access'] = 0;

                $stmt = $db->prepare('INSERT INTO ' . NV_AUTHORS_GLOBALTABLE . '_api_credential (admin_id, credential_title, credential_ident, credential_secret, api_roles, addtime, last_access) VALUES (:admin_id, :credential_title, :credential_ident, :credential_secret, :api_roles, ' . NV_CURRENTTIME . ', :last_access)');

                $stmt->bindParam(':credential_ident', $new_credential_ident, PDO::PARAM_STR);
                $stmt->bindParam(':credential_secret', $new_credential_secret_db, PDO::PARAM_STR);
                $stmt->bindParam(':last_access', $row['last_access'], PDO::PARAM_INT);

            } else {
                $stmt = $db->prepare('UPDATE ' . NV_AUTHORS_GLOBALTABLE . '_api_credential SET admin_id = :admin_id, credential_title = :credential_title, api_roles = :api_roles WHERE credential_ident="' . $row['credential_ident'].'"');
            }
            $stmt->bindParam(':admin_id', $row['admin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':credential_title',$row['credential_title'] , PDO::PARAM_STR);
            $stmt->bindParam(':api_roles', $row['api_roles'], PDO::PARAM_STR);

            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                if (empty($row['credential_ident'])) {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Add Credential', $new_credential_ident, $admin_info['userid']);
					$xtpl = new XTemplate('api-credentials-result.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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
					$xtpl->assign('CREDENTIAL_IDENT', $new_credential_ident);
                    $xtpl->assign('CREDENTIAL_SECRET', $new_credential_secret);
                    $xtpl->assign('URL_BACK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op);

					$xtpl->parse('main');
					$contents = $xtpl->text('main');

					$page_title = $lang_module['credential'];

					include NV_ROOTDIR . '/includes/header.php';
					echo nv_admin_theme($contents);
					include NV_ROOTDIR . '/includes/footer.php';
					die;
                } else {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit Credential', 'ID: ' . $new_credential_ident, $admin_info['userid']);
					nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
                }
                
            }
        } catch(PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); //Remove this line after checks finished
        }
    }
} elseif ($row['credential_ident'] != '') {
    $row = $db->query('SELECT * FROM ' . NV_AUTHORS_GLOBALTABLE . '_api_credential WHERE credential_ident="' . $row['credential_ident'] . '"')->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
} else {
    $row['admin_id'] = 0;
    $row['credential_title'] = '';
    $row['credential_ident'] = '';
    $row['api_roles'] = '';
}
	$db->sqlreset()->from(NV_AUTHORS_GLOBALTABLE . ' tb1');
    $db->join('INNER JOIN ' . NV_USERS_GLOBALTABLE . ' tb2 ON tb1.admin_id=tb2.userid');
    $db->select('tb1.admin_id, tb1.lev, tb2.username, tb2.first_name, tb2.last_name');
    $result = $db->query($db->sql());
    $array_admins = [];
    while ($row = $result->fetch()) {
        $row['full_name'] = nv_show_name_user($row['first_name'], $row['last_name']);
        $array_admins[$row['admin_id']] = $row;
    }

$array_api_roles_api = array();
$_sql = 'SELECT role_id,role_title FROM nv4_authors_api_role';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_api_roles_api[$_row['role_id']] = $_row;
}


$q = $nv_Request->get_title('q', 'post,get');

// Fetch Limit
$show_view = false;
if (!$nv_Request->isset_request('id', 'post,get')) {
    $show_view = true;
    $per_page = 20;
    $page = $nv_Request->get_int('page', 'post,get', 1);
    $db->sqlreset()
        ->select('COUNT(*)')
        ->from('' . NV_AUTHORS_GLOBALTABLE . '_api_credential');

    if (!empty($q)) {
        $db->where(' credential_title LIKE :q_credential_title OR credential_ident LIKE :q_credential_ident OR api_roles LIKE :q_api_roles');
    }
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_credential_title', '%' . $q . '%');
        $sth->bindValue(':q_credential_ident', '%' . $q . '%');
        $sth->bindValue(':q_api_roles', '%' . $q . '%');
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('addtime ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_credential_title', '%' . $q . '%');
        $sth->bindValue(':q_credential_ident', '%' . $q . '%');
        $sth->bindValue(':q_api_roles', '%' . $q . '%');
    }
    $sth->execute();
}

$xtpl = new XTemplate('api-credentials.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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

foreach ($array_admins as $userid => $value) {
    $xtpl->assign('OPTION', array(
        'key' => $userid,
        'title' => $value['username'],
        'selected' => ($userid == $row['admin_id']) ? ' selected="selected"' : ''
    ));
    $xtpl->parse('main.select_admin_id');
}
foreach ($array_api_roles_api as $key => $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['role_id'],
        'title' => $value['role_title'],
        'checked' => ($value['role_id'] == $row['api_roles']) ? ' checked="checked"' : ''
    ));
    $xtpl->parse('main.checkbox_api_roles');
}
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
        $view['admin_id'] = $array_admins[$view['admin_id']]['username'];
        $view['link_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;credential_ident=' . $view['credential_ident'];
        $view['link_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;delete_credential_ident=' . $view['credential_ident'] . '&amp;delete_checkss=' . md5($view['credential_ident'] . NV_CACHE_PREFIX . $client_info['session_id']);
        $xtpl->assign('VIEW', $view);
        $xtpl->parse('main.view.loop');
    }
    $xtpl->parse('main.view');
}


if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br />', $error));
    $xtpl->parse('main.error');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

$page_title = $lang_module['credential'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
