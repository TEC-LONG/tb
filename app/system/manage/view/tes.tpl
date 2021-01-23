<table class="table" width="99%" rel="jbsxBox">
	<thead>
		<tr>
			<th width="30">股票代码</th>
			<th width="50">股票名称</th>
			<th width="120">所属组</th>
			<th width="50">增减组</th>
			<th width="100">操作</th>
		</tr>
	</thead>
	<tbody>
		{foreach $info $row}
		<tr>
			<td>{$row.code}</td>
			<td>{$row.title}</td>
			<td>
				{foreach $groups as $_g}
					{if in_array($_g['id'], $row['groups'])}{$_g.name}{/if}
				{/foreach}
			</td>
			<td>
				<input type="hidden" class="system_manage_index_shares__id" value="{$row.id}">
				<select class="system_manage_index_group_id">
					<option value="0">.....</option>
					{foreach $groups as $_g}
						<option value="{$_g.id}">{$_g.name}</option>
					{/foreach}
				</select>
			</td>
			<td>
				<a href="javascript:void(0);" class="btnAdd" onclick="system_manage_index_group_add_func(this, 1)">加入所选组</a>
				<a href="javascript:void(0);" class="btnDel" onclick="system_manage_index_group_add_func(this, 2)">退出所选组</a>
			</td>
		</tr>
		{/foreach}
	</tbody>
</table>

<script type="text/javascript">
//type=1 表示加入  type=2 表示退出
var system_manage_index_group_add_func = function (obj, type) {

	var shares__id		= $(obj).parents('tr').find('.system_manage_index_shares__id').val();
	var this_group_id	= $(obj).parents('tr').find('.system_manage_index_group_id').val();

	if ( this_group_id==0 ) {
		alertMsg.warn('请选择组');
		return false;
	}
	
	var search_gp_url_post_data = {
		"type": type,
		"groups__id": this_group_id,
		"shares__id": shares__id,
		"code_or_name": $('#system_manage_index_code_or_name').val()
	};

	$('#system_manage_index_gp_table_div').loadUrl("{Fun::L('/system/manage/gupiao/search/by/code/or/name')}", search_gp_url_post_data, function (res) {
	
		// console.log(res);//将会得到返回的结果
	});
}

/// 加入组 或 退出组 提示
var system_manage_index_notice = '{$notice}';

if ( system_manage_index_notice!='no' ) {
	alertMsg.warn(system_manage_index_notice);
}
</script>

