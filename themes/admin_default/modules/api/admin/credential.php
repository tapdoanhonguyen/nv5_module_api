<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2020 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 23 Mar 2020 12:27:13 GMT
 */

if (!defined('NV_IS_FILE_ADMIN'))
    die('Stop!!!');

if ($nv_Request->isset_request('ajax_action', 'post')) {
    $credential_ident = $nv_Request->get_int('credential_ident', 'post', 0);
    $new_vid = $nv_Request->get_int('new_vid', 'post', 0);
    $content = 'NO_' . $credential_ident;
    if ($new_vid > 0)     {
        $sql = 'SELECT credential_ident FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential WHERE credential_ident!=' . $credential_ident . ' ORDER BY addtime ASC';
        $result = $db->query($sql);
        $addtime = 0;
        while ($row = $result->fetch())
        {
            ++$addtime;
            if ($addtime == $new_vid) ++$addtime;             $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_credential SET addtime=' . $addtime . ' WHERE credential_ident=' . $row['credential_ident'];
            $db->query($sql);
        }
        $sql = 'UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_credential SET addtime=' . $new_vid . ' WHERE credential_ident=' . $credential_ident;
        $db->query($sql);
        $content = 'OK_' . $credential_ident;
    }
    $nv_Cache->delMod($module_name);
    include NV_ROOTDIR . '/includes/header.php';
    echo $content;
    include NV_ROOTDIR . '/includes/footer.php';
}

if ($nv_Request->isset_request('delete_credential_ident', 'get') and $nv_Request->isset_request('delete_checkss', 'get')) {
    $credential_ident = $nv_Request->get_int('delete_credential_ident', 'get');
    $delete_checkss = $nv_Request->get_string('delete_checkss', 'get');
    if ($credential_ident > 0 and $delete_checkss == md5($credential_ident . NV_CACHE_PREFIX . $client_info['session_id'])) {
        $addtime=0;
        $sql = 'SELECT addtime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential WHERE credential_ident =' . $db->quote($credential_ident);
        $result = $db->query($sql);
        list($addtime) = $result->fetch(3);
        
        $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential  WHERE credential_ident = ' . $db->quote($credential_ident));
        if ($addtime > 0)         {
            $sql = 'SELECT credential_ident, addtime FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential WHERE addtime >' . $addtime;
            $result = $db->query($sql);
            while (list($credential_ident, $addtime) = $result->fetch(3))
            {
                $addtime--;
                $db->query('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_credential SET addtime=' . $addtime . ' WHERE credential_ident=' . intval($credential_ident));
            }
        }
        $nv_Cache->delMod($module_name);
        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Credential', 'ID: ' . $credential_ident, $admin_info['userid']);
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
}

$row = array();
$error = array();
$row['credential_ident'] = $nv_Request->get_int('credential_ident', 'post,get', 0);
if ($nv_Request->isset_request('submit', 'post')) {
    $row['admin_id'] = $nv_Request->get_int('admin_id', 'post', 0);
    $row['credential_title'] = $nv_Request->get_title('credential_title', 'post', '');

    $_api_roles = $nv_Request->get_array('api_roles', 'post');
    $row['api_roles'] = !empty($_api_roles) ? implode(',', $_api_roles) : '';

    if (empty($row['credential_title'])) {
        $error[] = $lang_module['error_required_credential_title'];
    }

    if (empty($error)) {
        try {
            if (empty($row['credential_ident'])) {
                $row['credential_secret'] = '';
                $row['edittime'] = 0;
                $row['last_access'] = 0;

                $stmt = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_credential (admin_id, credential_title, credential_ident, credential_secret, api_roles, addtime, edittime, last_access) VALUES (:admin_id, :credential_title, :credential_secret, :api_roles, :addtime, :edittime, :last_access)');

                $stmt->bindParam(':credential_secret', $row['credential_secret'], PDO::PARAM_STR);
                $weight = $db->query('SELECT max(addtime) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential')->fetchColumn();
                $weight = intval($weight) + 1;
                $stmt->bindParam(':addtime', $weight, PDO::PARAM_INT);

                $stmt->bindParam(':edittime', $row['edittime'], PDO::PARAM_INT);
                $stmt->bindParam(':last_access', $row['last_access'], PDO::PARAM_INT);

            } else {
                $stmt = $db->prepare('UPDATE ' . NV_PREFIXLANG . '_' . $module_data . '_credential SET admin_id = :admin_id, credential_title = :credential_title, api_roles = :api_roles WHERE credential_ident=' . $row['credential_ident']);
            }
            $stmt->bindParam(':admin_id', $row['admin_id'], PDO::PARAM_INT);
            $stmt->bindParam(':credential_title', $row['credential_title'], PDO::PARAM_STR);
            $stmt->bindParam(':api_roles', $row['api_roles'], PDO::PARAM_STR);

            $exc = $stmt->execute();
            if ($exc) {
                $nv_Cache->delMod($module_name);
                if (empty($row['credential_ident'])) {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Add Credential', ' ', $admin_info['userid']);
                } else {
                    nv_insert_logs(NV_LANG_DATA, $module_name, 'Edit Credential', 'ID: ' . $row['credential_ident'], $admin_info['userid']);
                }
                nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
            }
        } catch(PDOException $e) {
            trigger_error($e->getMessage());
            die($e->getMessage()); //Remove this line after checks finished
        }
    }
} elseif ($row['credential_ident'] > 0) {
    $row = $db->query('SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_credential WHERE credential_ident=' . $row['credential_ident'])->fetch();
    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
    }
} else {
    $row['admin_id'] = 0;
    $row['credential_title'] = '';
    $row['credential_ident'] = '';
    $row['api_roles'] = '';
}
$array_admin_id_users = array();
$_sql = 'SELECT userid,username FROM nv4_users';
$_query = $db->query($_sql);
while ($_row = $_query->fetch()) {
    $array_admin_id_users[$_row['userid']] = $_row;
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
        ->from('' . NV_PREFIXLANG . '_' . $module_data . '_credential');

    if (!empty($q)) {
        $db->where('admin_id LIKE :q_admin_id OR credential_title LIKE :q_credential_title OR credential_ident LIKE :q_credential_ident OR api_roles LIKE :q_api_roles OR last_access LIKE :q_last_access');
    }
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_admin_id', '%' . $q . '%');
        $sth->bindValue(':q_credential_title', '%' . $q . '%');
        $sth->bindValue(':q_credential_ident', '%' . $q . '%');
        $sth->bindValue(':q_api_roles', '%' . $q . '%');
        $sth->bindValue(':q_last_access', '%' . $q . '%');
    }
    $sth->execute();
    $num_items = $sth->fetchColumn();

    $db->select('*')
        ->order('addtime ASC')
        ->limit($per_page)
        ->offset(($page - 1) * $per_page);
    $sth = $db->prepare($db->sql());

    if (!empty($q)) {
        $sth->bindValue(':q_admin_id', '%' . $q . '%');
        $sth->bindValue(':q_credential_title', '%' . $q . '%');
        $sth->bindValue(':q_credential_ident', '%' . $q . '%');
        $sth->bindValue(':q_api_roles', '%' . $q . '%');
        $sth->bindValue(':q_last_access', '%' . $q . '%');
    }
    $sth->execute();
}

$xtpl = new XTemplate('credential.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
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

foreach ($array_admin_id_users as $value) {
    $xtpl->assign('OPTION', array(
        'key' => $value['userid'],
        'title' => $value['username'],
        'selected' => ($value['userid'] == $row['admin_id']) ? ' selected="selected"' : ''
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
        for($i = 1; $i <= $num_items; ++$i) {
            $xtpl->assign('WEIGHT', array(
                'key' => $i,
                'title' => $i,
                'selected' => ($i == $view['addtime']) ? ' selected="selected"' : ''));
            $xtpl->parse('main.view.loop.addtime_loop');
        }
        $view['admin_id'] = $array_admin_id_users[$view['admin_id']]['username'];
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
