<div class="pageContent">
	<form method="post" action="{Fun::L('/system/manage/doc/post')}" class="pageForm required-validate" onsubmit="return validateCallback(this, permissionAjaxDone);">
		{if isset($row.id)}
		<input type="hidden" name="id" value="{$row.id}">
		{/if}
        <div class="pageFormContent" layoutH="56">
            <p>
				<label>文档标题：</label>
				<input class="required" name="title" type="text" value="{if isset($row)}{$row.title}{/if}"/>
			</p>
            <p>
				<label>备注说明：</label>
				<textarea name="descr" cols="32" rows="7">{if isset($row)&&!empty($row.descr)}{$row.descr}{/if}</textarea>
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
	
	{literal}
	/// re = {statusCode: 200, message: "操作成功", navTabId: "tools_prorecord_detad"}
	if (re.statusCode==200) {
		alertMsg.correct(re.message);
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