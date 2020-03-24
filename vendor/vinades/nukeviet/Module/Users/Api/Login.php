<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Jun 20, 2010 8:59:32 PM
 */

namespace NukeViet\Module\Users\Api;

use NukeViet\Api\Api;
use NukeViet\Api\ApiResult;
use NukeViet\Api\IApi;
use PDO;

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

class Login implements IApi
{
    private $result;

    /**
     * @return number
     */
    public static function getAdminLev()
    {
        return Api::ADMIN_LEV_MOD;
    }

    /**
     * @return string
     */
    public static function getCat()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     * @see \NukeViet\Api\IApi::setResultHander()
     */
    public function setResultHander(ApiResult $result)
    {
        $this->result = $result;
    }

    /**
     * {@inheritDoc}
     * @see \NukeViet\Api\IApi::execute()
     */
    public function execute()
    {
        global $nv_Lang, $nv_Request, $db, $nv_Cache, $global_config, $crypt;

        $module_name = Api::getModuleName();
        $module_info = Api::getModuleInfo();
        $module_data = $module_info['module_data'];
        $admin_id = Api::getAdminId();

        $nv_username = $nv_Request->get_title('login', 'post', '', 1);
        $nv_password = $nv_Request->get_title('password', 'post', '');
        if ($global_config['captcha_type'] == 2) {
            $nv_seccode = $nv_Request->get_title('g-recaptcha-response', 'post', '');
        } else {
            $nv_seccode = $nv_Request->get_title('nv_seccode', 'post', '');
        }

        $check_seccode = !$gfx_chk ? true : (nv_capcha_txt($nv_seccode) ? true : false);

        if (!$check_seccode) {
			$this->result->setMessage($nv_Lang->getModule('securitycodeincorrect'));
        }

        if (empty($nv_username)) {
			$this->result->setMessage($nv_Lang->getModule('username_empty'));
        }

        if (empty($nv_password)) {
			$this->result->setMessage($nv_Lang->getModule('password_empty'));
        }

        if (defined('NV_IS_USER_FORUM')) {
            $error = '';
            require_once NV_ROOTDIR . '/' . $global_config['dir_forum'] . '/nukeviet/login.php';
            if (!empty($error)) {
				$this->result->setMessage($error);
            }
        } else {
            $error1 = $nv_Lang->getModule('loginincorrect');
			$error = '';
            $check_email = nv_check_valid_email($nv_username, true);
            if ($check_email[0] == '') {
                // Email login
                $sql = "SELECT * FROM " . NV_USERS_GLOBALTABLE . " WHERE email =" . $db->quote($check_email[1]);
                $row = $db->query($sql)->fetch();
                if (empty($row)) {
					$this->result->setMessage($nv_Lang->getModule('loginincorrect'));
					$error = $nv_Lang->getModule('loginincorrect');
                }

                if ($row['email'] != $nv_username) {
					$this->result->setMessage($nv_Lang->getModule('loginincorrect'));
					$error = $nv_Lang->getModule('loginincorrect');
                }
            } else {
                // Username login
                $sql = "SELECT * FROM " . NV_USERS_GLOBALTABLE . " WHERE md5username ='" . nv_md5safe($nv_username) . "'";
                $row = $db->query($sql)->fetch();
                if (empty($row)) {
                    $this->result->setMessage($nv_Lang->getModule('loginincorrect'));
					$error = $nv_Lang->getModule('loginincorrect');
                }

                if ($row['username'] != $nv_username) {
                    $this->result->setMessage($nv_Lang->getModule('loginincorrect'));
					$error = $nv_Lang->getModule('loginincorrect');
                }
            }

            if (!$crypt->validate_password($nv_password, $row['password'])) {
                $this->result->setMessage($nv_Lang->getModule('loginincorrect'));
				$error = $nv_Lang->getModule('loginincorrect');
            }

            if ($row['safemode'] == 1) {
				$this->result->setMessage($nv_Lang->getModule('safe_deactivate_openidreg'));
				$error = $nv_Lang->getModule('safe_deactivate_openidreg');
            }

            if (!$row['active']) {
				$this->result->setMessage($nv_Lang->getModule('login_no_active'));
				$error = $nv_Lang->getModule('login_no_active');
            }
			if(empty($error)){
				$nv_Cache->delMod($module_name);
                $this->result->setSuccess();
			}
        }

        return $this->result->getResult();
    }
}
