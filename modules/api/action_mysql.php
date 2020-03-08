<?php

/**
 * @Project NUKEVIET 4.x
 * @Author NV Holding <ceo@nvholding.vn>
 * @Copyright (C) 2020 NV Holding. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 07 Mar 2020 09:01:23 GMT
 */

if (!defined('NV_MAINFILE'))
    die('Stop!!!');

$sql_drop_module = array();
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_credential";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $module_data . "_role";

$sql_create_module = $sql_drop_module;
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix']  . "_authors_api_credential (
admin_id int(11) unsigned NOT NULL,
  credential_title varchar(255) NOT NULL DEFAULT '',
  credential_ident varchar(50) NOT NULL DEFAULT '',
  credential_secret varchar(250) NOT NULL DEFAULT '',
  api_roles varchar(255) NOT NULL DEFAULT '',
  addtime int(11) NOT NULL DEFAULT '0',
  edittime int(11) NOT NULL DEFAULT '0',
  last_access int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY credential_ident (credential_ident),
  UNIQUE KEY credential_secret (credential_secret(191)),
  KEY admin_id (admin_id)
) ENGINE=MyISAM;";

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_authors_api_role (
role_id smallint(4) NOT NULL AUTO_INCREMENT,
  role_title varchar(250) NOT NULL DEFAULT '',
  role_description text NOT NULL,
  role_data text NOT NULL,
  addtime int(11) NOT NULL DEFAULT '0',
  edittime int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (role_id)
) ENGINE=MyISAM;";
