<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC <contact@vinades.vn>
 * @Copyright (C) 2014 VINADES ., JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Jun 20, 2010 8:59:32 PM
 */

namespace NukeViet\Module\News\Api;

use NukeViet\Api\Api;
use NukeViet\Api\ApiResult;
use NukeViet\Api\IApi;
use PDO;

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    die('Stop!!!');
}

class ListNewsItems implements IApi
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
        global $nv_Lang, $nv_Request, $db, $nv_Cache;

        $module_name = Api::getModuleName();
        $module_info = Api::getModuleInfo();
        $module_data = $module_info['module_data'];
        $admin_id = Api::getAdminId();

        // Get Config Module
        $page_config = [];
        $db->sqlreset()
            ->select('*')
            ->from(NV_PREFIXLANG . '_' . $module_data . '_rows ')
            ->limit(20);
		$listrow = $db->query($db->sql());
		$data = [];
		while ($row = $listrow->fetch(5)){
			$data[]=$row;
		}
		
		if ($listrow->rowCount()>0) {
			$this->result->setMessage(json_encode($data));
			$this->result->setSuccess();
		} else {
			$this->result->setMessage($nv_Lang->getModule('No Data'));
		}
        

        return $this->result->getResult();
    }
}
