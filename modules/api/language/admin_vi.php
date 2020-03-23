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

$lang_translator['author'] = 'NV Holding (ceo@nvholding.vn)';
$lang_translator['createdate'] = '07/03/2020, 09:01';
$lang_translator['copyright'] = '@Copyright (C) 2020 NV Holding All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

$lang_module['main'] = 'Trang chính';
$lang_module['config'] = 'Cấu hình';
$lang_module['save'] = 'Lưu lại';

$lang_module['api_addtime'] = 'Tạo lúc';
$lang_module['api_edittime'] = 'Cập nhật';
$lang_module['api_roles'] = 'Quản lý API Roles';
$lang_module['api_roles_list'] = 'Danh sách API Roles';
$lang_module['api_roles_empty'] = 'Chưa có API Role nào được thêm. Mời bạn hoàn thành mẫu bên dưới để thêm mới API Role';
$lang_module['api_roles_add'] = 'Tạo mới API Role';
$lang_module['api_roles_edit'] = 'Sửa API Role';
$lang_module['api_roles_title'] = 'Tên gọi API Role';
$lang_module['api_roles_description'] = 'Mô tả API Role';
$lang_module['api_roles_allowed'] = 'Các API được phép truy cập';
$lang_module['api_roles_error_title'] = 'Lỗi: Chưa nhập tên gọi API Role';
$lang_module['api_roles_error_exists'] = 'Lỗi: Tên gọi API Role này đã có, mời nhập tên gọi khác để tránh nhầm lẫn';
$lang_module['api_roles_error_role'] = 'Lỗi: Chưa có API nào được chọn';
$lang_module['api_roles_checkall'] = 'Chọn tất cả';
$lang_module['api_roles_uncheckall'] = 'Bỏ chọn tất cả';
$lang_module['api_roles_detail'] = 'Chi tiết các API của';
$lang_module['api_role_notice'] = 'Lưu ý: Tùy theo cấp độ của tài khoản quản trị được cấp phép mà các API được quyền sử dụng trong mỗi API Role sẽ được xác định lại';
$lang_module['api_role_notice_lang'] = 'Các API của hệ thống sẽ hiệu lực đối với tất cả các ngôn ngữ. Các API của module chỉ có hiệu lực đối với ngôn ngữ hiện tại.';

$lang_module['api_of_system'] = 'Hệ thống';

$lang_module['api_cr'] = 'Quyền truy cập API';
$lang_module['api_cr_error_role_empty'] = 'Chưa có API Role được tạo, hãy tạo API Role trước. Hệ thống sẽ tự động chuyển trến trang tạo API Role trong giây lát';
$lang_module['api_remote_off'] = 'Remote API <strong>đang tắt</strong>, các tài khoản được phép truy cập API bên dưới sẽ không thể thực hiện các cuộc gọi API. Để thực hiện được cuộc gọi API, hãy <strong><a href="%s" target="_blank">bật Remote API tại đây</a></strong>';
$lang_module['api_cr_last_access_none'] = 'Chưa';
$lang_module['api_cr_last_access'] = 'Dùng gần đây';
$lang_module['api_cr_add'] = 'Thêm quyền truy cập API';
$lang_module['api_cr_edit'] = 'Sửa quyền truy cập API';
$lang_module['api_cr_title'] = 'Mô tả quyền';
$lang_module['api_cr_for_admin'] = 'Chọn quản trị';
$lang_module['api_cr_roles'] = 'Chọn API Role';
$lang_module['api_cr_roles1'] = 'API Role';
$lang_module['api_cr_error_title'] = 'Lỗi: Vui lòng nhập mô tả quyền truy cập API';
$lang_module['api_cr_error_admin'] = 'Lỗi: Vui lòng chọn quản trị';
$lang_module['api_cr_error_roles'] = 'Lỗi: Vui lòng chọn API Role';
$lang_module['api_cr_result'] = 'Dưới đây là khóa truy cập và mã bí mật. Bạn cần lưu trữ lại mã bí mật ở một nơi an toàn trước khi thoát khỏi màn hình này. Sau khi thoát khỏi màn hình này bạn sẽ không thể lấy lại mã bí mật. Nếu bị mất khóa truy cập và mã bí mật bạn cần phải tạo lại quyền truy cập API khác.';
$lang_module['api_cr_credential_ident'] = 'Khóa truy cập';
$lang_module['api_cr_credential_secret'] = 'Mã bí mật';
$lang_module['api_cr_back'] = 'Đã sao chép xong';
$lang_module['api_cr_notice'] = 'Chú ý: Giữ an toàn cho mã bí mật và khóa truy cập đặc biệt là mã bí mật. Nếu mã bí mật và khóa truy cập bị lộ, kẻ phá hoại có thể thực hiện các thao tác không mong muốn';

$lang_module['api_System'] = 'Các chức năng hệ thống';
$lang_module['api_System_SendMail'] = 'Gửi email';

$lang_module['other_info'] = 'Thông tin khác';

//Lang for function role
$lang_module['role'] = 'role';
$lang_module['add'] = 'Thêm mới';
$lang_module['edit'] = 'Sửa';
$lang_module['delete'] = 'Xóa';
$lang_module['number'] = 'STT';
$lang_module['active'] = 'Trạng thái';
$lang_module['search_title'] = 'Nhập từ khóa tìm kiếm';
$lang_module['search_submit'] = 'Tìm kiếm';
$lang_module['role_title'] = 'Role title';
$lang_module['role_description'] = 'Role description';
$lang_module['role_data'] = 'Role data';
$lang_module['error_required_role_title'] = 'Lỗi: bạn cần nhập dữ liệu cho Role title';
$lang_module['error_required_role_data'] = 'Lỗi: bạn cần nhập dữ liệu cho Role data';

//Lang for function credential
$lang_module['credential'] = 'credential';
$lang_module['admin_id'] = 'Admin id';
$lang_module['credential_title'] = 'Credential title';
$lang_module['credential_ident'] = 'Credential ident';
$lang_module['credential_secret'] = 'Credential secret';
$lang_module['edittime'] = 'Edittime';
$lang_module['last_access'] = 'Last access';

//Lang for function credential
$lang_module['error_required_credential_title'] = 'Lỗi: bạn cần nhập dữ liệu cho Credential title';
