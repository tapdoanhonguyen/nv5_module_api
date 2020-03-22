/**
 * @Project NUKEVIET 4.x
 * @Author NV Holding <ceo@nvholding.vn>
 * @Copyright (C) 2020 NV Holding. All rights reserved
 * @License: Not free read more http://nukeviet.vn/vi/store/modules/nvtools/
 * @Createdate Sat, 07 Mar 2020 09:01:23 GMT
 */



function apiRoleChanged() {
    var totalApis = 0;
    $('[data-toggle="apicat"]').each(function() {
        var $this = $(this);
        var ctnItem = $($this.attr('href'));
        var total = ctnItem.find('[data-toggle="apiroleit"]:checked').length;
        if (total > 0) {
            totalApis = totalApis + total;
            var textEle = $this.find('span');
            if (textEle.length) {
                textEle.html('(' + total + ')');
            } else {
                $this.append(' <span>(' + total + ')</span>');
            }
        } else {
            $this.find('span').remove();
        }
    });
    if (totalApis > 0) {
        var textEle = $('#apiRoleAll').find('span');
        if (textEle.length) {
            textEle.html('(' + totalApis + ')');
        } else {
            $('#apiRoleAll').append(' <span>(' + totalApis + ')</span>');
        }
    } else {
        $('#apiRoleAll').find('span').remove();
    }
}

$("#checkall").click(function(){
    $("input[name='modules[]']:checkbox").prop("checked", true);
});
$("#uncheckall").click(function() {
    $("input[name='modules[]']:checkbox").prop("checked", false);
});

$(document).ready(function() {
    $('[data-toggle="apiroleit"]').change(function() {
        apiRoleChanged();
    });
    $('[data-toggle="apicat"]').click(function(e) {
        e.preventDefault();
        $('[data-toggle="apicat"]').removeClass('active');
        $(this).addClass('active');
        $('[data-toggle="apichid"]').hide();
        $($(this).attr('href')).show();
        $('[name="current_cat"]').val($(this).data('cat'));
    });
    $('[data-toggle="apicheck"]').click(function(e) {
        e.preventDefault();
        $($(this).attr('href')).find('[type="checkbox"]').prop('checked', true);
        apiRoleChanged();
    });
    $('[data-toggle="apiuncheck"]').click(function(e) {
        e.preventDefault();
        $($(this).attr('href')).find('[type="checkbox"]').prop('checked', false);
        apiRoleChanged();
    });
    $('[data-toggle="apiroledel"]').click(function(e) {
        e.preventDefault();
        if (confirm(nv_is_del_confirm[0])) {
            $.post(script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=api-roles&nocache=' + new Date().getTime(), 'del=1&role_id=' + $(this).data('id'), function(res) {
                if (res == 'OK') {
                    location.reload();
                } else {
                    alert(nv_is_del_confirm[2]);
                }
            });
        }
    });
    $('[data-toggle="apicerdel"]').click(function(e) {
        e.preventDefault();
        if (confirm(nv_is_del_confirm[0])) {
            $.post(script_name + '?' + nv_lang_variable + '=' + nv_lang_data + '&' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=api-credentials&nocache=' + new Date().getTime(), 'del=1&credential_ident=' + $(this).data('id'), function(res) {
                if (res == 'OK') {
                    location.reload();
                } else {
                    alert(nv_is_del_confirm[2]);
                }
            });
        }
    });
});
