<!-- BEGIN: main -->
<!-- BEGIN: view -->
<div class="well">
<form action="{NV_BASE_ADMINURL}index.php" method="get">
    <input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
    <input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
    <input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
    <div class="row">
        <div class="col-xs-24 col-md-6">
            <div class="form-group">
                <input class="form-control" type="text" value="{Q}" name="q" maxlength="255" placeholder="{LANG.search_title}" />
            </div>
        </div>
        <div class="col-xs-12 col-md-3">
            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
            </div>
        </div>
    </div>
</form>
</div>
<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    
                    <th>{LANG.role_title}</th>
                    <th>{LANG.role_description}</th>
					<th >{LANG.addtime}</th>
					<th >{LANG.edittime}</th>
                    <th class="w150">&nbsp;</th>
                </tr>
            </thead>
            <!-- BEGIN: generate_page -->
            <tfoot>
                <tr>
                    <td class="text-center" colspan="4">{NV_GENERATE_PAGE}</td>
                </tr>
            </tfoot>
            <!-- END: generate_page -->
            <tbody>
                <!-- BEGIN: loop -->
                <tr>
                    
                    <td> {VIEW.role_title} </td>
					<td> {VIEW.role_description} </td>
                    <td> {VIEW.addtime} </td>
                    <td> {VIEW.edittime} </td>
                    <td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}#edit">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
                </tr>
                <!-- END: loop -->
            </tbody>
        </table>
    </div>
</form>
<!-- END: view -->

<!-- BEGIN: error -->
<div class="alert alert-warning">{ERROR}</div>
<!-- END: error -->
<div class="panel panel-default">
<div class="panel-body">
<form class="form-horizontal" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
    <input type="hidden" name="role_id" value="{ROW.role_id}" />
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.role_title}</strong> <span class="red">(*)</span></label>
        <div class="col-sm-19 col-md-20">
            <input class="form-control" type="text" name="role_title" value="{ROW.role_title}" required="required" oninvalid="setCustomValidity(nv_required)" oninput="setCustomValidity('')" />
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.role_description}</strong></label>
        <div class="col-sm-19 col-md-20">
            <textarea class="form-control" style="height:100px;" cols="75" rows="5" name="role_description">{ROW.role_description}</textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-5 col-md-4 control-label"><strong>{LANG.api_access}</strong> </label>
        <div class="col-sm-19 col-md-20">
            <div class="form-group row">
				<div class="col-12 col-sm-3">
					<div class="root-api-actions">
						<ul>
							<!-- BEGIN: cat_module -->
								<li><a data-toggle="apicat" data-cat="{apilev1.key}" href="#api-child-{apilev1.key}" class="{ACTIVE}">{apilev1.name} </a></li>
								<!-- BEGIN: sub_cat -->
									<li><a data-toggle="apicat" data-cat="{apilev2.key}" href="#api-child-{apilev2.key}" class="{ACTIVESUB}">{apilev2.name} </a></li>
								<!-- END: sub_cat -->
							<!-- END: cat_module -->
						</ul>
					</div>
				</div>
				<div class="col-12 col-sm-9">
					<div class="child-apis">
						<div class="panel-body">
							
							<!-- BEGIN: tab_module -->
							<div data-toggle="apichid" class="child-apis-item" id="api-child-about" style="display: none;">
								<div class="child-apis-item-ctn">
									<div class="row">
																					<div class="col-12 col-sm-6">
											<label class="custom-control custom-checkbox my-1">
												<input data-toggle="apiroleit" class="custom-control-input" type="checkbox" name="api_about[]" value="CreatArticle"><span class="custom-control-label">Tạo bài viết</span>
											</label>
										</div>
																				</div>
								</div>
								<div class="child-apis-item-tool">
									<hr>
									<ul class="list-inline list-unstyled">
										<li class="list-inline-item"><a href="#api-child-about" data-toggle="apicheck"><i class="fas fa-check-circle text-muted"></i> Chọn tất cả</a></li>
										<li class="list-inline-item"><a href="#api-child-about" data-toggle="apiuncheck"><i class="fas fa-circle text-muted"></i> Bỏ chọn tất cả</a></li>
									</ul>
								</div>
							</div>
							<!-- END: tab_module -->
						</div>                              
					</div>
				</div>
            </div>
        </div>
    </div>
    <div class="form-group" style="text-align: center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>
</div></div>

<script type="text/javascript">
//<![CDATA[
    function nv_change_weight(id) {
        var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
        var new_vid = $('#id_weight_' + id).val();
        $.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=role&nocache=' + new Date().getTime(), 'ajax_action=1&role_id=' + id + '&new_vid=' + new_vid, function(res) {
            var r_split = res.split('_');
            if (r_split[0] != 'OK') {
                alert(nv_is_change_act_confirm[2]);
            }
            window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=role';
            return;
        });
        return;
    }


//]]>
</script>
<!-- END: main -->