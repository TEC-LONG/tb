<div class="pageContent">
	<form method="post" action="{Fun::L('/system/manage/permission/post')}" class="pageForm required-validate" onsubmit="return validateCallback(this, permissionAjaxDone);">
		{if isset($row.id)}
		<input type="hidden" name="id" value="{$row.id}">
		{/if}
        <div class="pageFormContent" layoutH="56">
            <p>
				<label>权限名称：</label>
				<input class="required" name="name" type="text" value="{if isset($row)}{$row.name}{/if}"/>
			</p>
            <p>
				<label>权限标识：</label>
				<select class="combox" name="flag">
					{foreach $flag as $k=>$v}
					<option value="{$k}" {if isset($row)&&$k==$row.flag}selected{/if}>{$v}</option>
					{/foreach}
				</select>
			</p>
		</div>
		<div class="formBar">
			<ul>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
				<li>
					<div class="button"><div class="buttonContent"><button type="button" class="close">取消</button></div></div>
				</li>
			</ul>
		</div>
	</form>
</div>
<script>
var permissionAjaxDone = function (re) {
	
	// var tmp_url = '{Fun::L("/system/manage/permission/list")}';
	{literal}
	/// re = {statusCode: 200, message: "操作成功", navTabId: "tools_prorecord_detad"}
	if (re.statusCode==200) {
		alertMsg.correct(re.message);
		console.log(re);
		if (re.navTabId){
			navTab.reloadFlag(re.navTabId);
		}
		$.pdialog.closeCurrent();
	}else{
		alertMsg.error(re.message);
	}
	{/literal}
}
</script>