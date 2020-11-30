<div class="pageContent">
	<form method="post" action="" class="pageForm required-validate" onsubmit="return validateCallback(this, menuPermissionAjaxDone);">
		{if isset($row.id)}
		<input type="hidden" name="id" value="{$row.id}">
		{/if}
        <div class="pageFormContent" layoutH="56">
			<p>
				<label>页面名称：</label>
				<input class="required" name="display_name" type="text" value="{if isset($row)}{$row.display_name}{/if}"/>
			</p>
            <p>
				<label>权限名称：</label>
				<input class="required" name="permission.name" type="text" value="{if isset($row)}{$row.pname}{/if}" readonly/>
				<input name="permission._id" type="hidden" value="{if isset($row)}{$row.permission__id}{/if}"/>
				<input name="permission.flag" type="hidden" value="{if isset($row)}{$flag[$row.flag]}{/if}"/>
				<a class="btnLook" href="{Fun::L('/system/manage/permission/list')}?lookup=1" lookupGroup="permission">查找带回</a>
			</p>
            <p>
				<label>路由：</label>
				<input name="route" type="text" value="{if isset($row)}{$row.route}{/if}"/>
			</p>
			<p>
				<label>上级id：</label>
				<input name="parent_id" type="text" value="{if isset($row)}{$row.parent_id}{else}0{/if}"/>
				<!-- <input class="required" name="menu.name" type="text" value="{if isset($row)}{$row.name}{/if}" readonly/> -->
				<!-- <input  name="menu._id" type="hidden" value="{if isset($row)}{$row.menu__id}{/if}"/>
				<a class="btnLook" href="{$url.menuLookup.url}?lookup=1" lookupGroup="menu" width="1700" height="600">查找带回</a> -->
			</p>
			<p>
				<label>请求方式：</label>
				<select class="combox" name="request">
					{foreach $mp_request as $k=>$v}
					<option value="{$k}" {if isset($row)&&$k==$row.request}selected{/if}>{$v}</option>
					{/foreach}
				</select>
			</p>
			<p>
				<label>3级菜单跳转类型：</label>
				<select class="combox" name="level3_type">
					{foreach $mp_lv3_type as $k=>$v}
					<option value="{$k}" {if isset($row)&&$k==$row.level3_type}selected{/if}>{$v}</option>
					{/foreach}
				</select>
			</p>
            <p>
				<label>外部跳转链接：</label>
				<input name="level3_href" type="text" value="{if isset($row)}{$row.level3_href}{/if}"/>
			</p>
            <p>
				<label>navtab：</label>
				<input name="navtab" type="text" value="{if isset($row)}{$row.navtab}{/if}"/>
			</p>
            <p>
				<label>排序：</label>
				<input name="sort" type="text" class="digits" min="0" max="100" value="{if isset($row)}{$row.sort}{/if}"/>
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
var menuPermissionAjaxDone = function (re) {
	
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