<div class="pageContent">
	<form method="post" action="{$url.adh.url}" class="pageForm required-validate" onsubmit="return validateCallback(this, navTabAjaxDone);">
        <div class="pageFormContent" layoutH="56">
            <p>
				<label>账号：</label>
				<input name="acc" type="text" class="required" />
			</p>
			<p>
				<label>密码：</label>
				<input name="pwd" type="text" class="required alphanumeric" minlength="6" maxlength="20" />
			</p>
			<p>
				<label>所属组：</label>
				<select class="combox" name="user_group__id">
					<option value="">请选择...</option>
					{foreach $user_group as $v}
					<option value="{$v['id']}">{$v['name']}</option>
					{/foreach}
				</select>
			</p>
			<div class="divider"></div>
			<p>
				<label>昵称：</label>
				<input name="nickname" type="text" class="required" />
			</p>
			<p>
				<label>手机号：</label>
				<input name="cell" type="text" class="phone" />
			</p>
			<p>
				<label>邮箱：</label>
				<input name="email" type="text" class="email" />
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