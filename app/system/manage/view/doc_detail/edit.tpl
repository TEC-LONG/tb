<div class="pageContent">
	<form method="post" action="{Fun::L('/system/manage/doc/mulu/post')}" class="pageForm required-validate" onsubmit="return validateCallback(this, permissionAjaxDone);">
		{if isset($row.id)}
		<input type="hidden" name="id" value="{$row.id}">
		{/if}
		<input type="hidden" name="doc__id" value="{$doc__id}">
        <div class="pageFormContent" layoutH="56">
            <p>
				<label>目录项标题：</label>
				<input class="required" name="title" type="text" value="{if isset($row)}{$row.title}{/if}"/>
			</p>
			<p>
				<label>排序值：</label>
				<input class="digits" name="sort" type="text" value="{if isset($row)}{$row.sort}{else}0{/if}"/>
			</p>
            <p>
				<label>上级目录：</label>
				<input name="system_manage_docMuluEdit_parent_mulu.id" value="0" type="hidden"/>
				<input name="system_manage_docMuluEdit_parent_mulu.level" value="0" type="hidden"/>
				<input name="system_manage_docMuluEdit_parent_mulu.title" value="" type="text" readonly/>
				<a class="btnLook" href="{Fun::L('/system/manage/doc/mulu/lookup')}?id={$doc__id}" lookupGroup="system_manage_docMuluEdit_parent_mulu" width="400" height="600">上级目录</a>	
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