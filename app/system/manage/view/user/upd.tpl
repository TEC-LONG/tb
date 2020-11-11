<div class="pageContent">
	<form method="post" action="{$url.updh.url}?id={$row.id}" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
				<label>账号：</label>
				<input name="acc" type="text" class="required" value="{$row.acc}" />
			</p>
			<p>
				<label>修改密码：</label>
				<input name="pwd" type="text" class="alphanumeric" minlength="6" maxlength="20" alt="不填写则表示不修改密码" />
			</p>
			<p>
				<label>所属组：</label>
				<select class="combox" name="user_group__id">
					<option value="">请选择...</option>
					{foreach $user_group as $v}
					<option value="{$v['id']}" {if $v['id']==$row.user_group__id}selected{/if}>{$v['name']}</option>
					{/foreach}
				</select>
			</p>
			<div class="divider"></div>
			<p>
				<label>昵称：</label>
				<input name="nickname" type="text" class="required" value="{$row.nickname}" />
			</p>
			<p>
				<label>手机号：</label>
				<input name="cell" type="text" class="phone" value="{$row.cell}" />
			</p>
			<p>
				<label>邮箱：</label>
				<input name="email" type="text" class="email" value="{$row.email}" />
			</p>
			<div class="divider"></div>
			<p class="nowrap">
				<label>头像：</label>
				<div class="upload-wrap">
					<input type="file" name="img" accept="image/*" class="valid" style="left: 0px;">
				</div>
			</p>
			<div class="divider"></div>
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