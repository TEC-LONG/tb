
{literal}
<style>
label {padding-right: 35px;}
 input {vertical-align:middle;margin-left: 3px;}
</style>
{/literal}
<form method="post" action="{Fun::L('/system/manage/user/group/permission/post')}" class="pageForm required-validate" onsubmit="return validateCallback(this, gpAjaxDone);">
	<input type="hidden" name="user_group__id" value="{$search.id}">
<div class="pageContent" layoutH="42" style="vertical-align:middle;">
{foreach $menu as $v1}
<div>
	<h1 class="gpc1 p1-{$v1.lv1.id}">
		<span class="gp_contro" style="margin-top:4px;margin-left:3px;cursor:pointer;">{$v1.lv1.display_name}</span>
		<input class="gp_main" type="checkbox" name="mp_id[]" value="{$v1.lv1.id}" {if in_array($v1.lv1.id, $power)}checked{/if}/>
	</h1>
	<div class="divider"></div>
	{foreach $v1.lv2 as $v2}
	<div class="panel collapse" minH="100" defH="{if isset($v2.son)}{count($v2.son)*66}{/if}">
		<h1 class="p2-{$v2.menu.id}">
			<span class="gp_contro gpc2" lv="p1-{$v1.lv1.id}" style="padding-top:7px;cursor:pointer;">{$v2.menu.display_name}</span>
			<input class="gp_main" type="checkbox" name="mp_id[]" value="{$v2.menu.id}" {if in_array($v2.menu.id, $power)}checked{/if}/ lv="p1-{$v1.lv1.id}">
		</h1>
		{if isset($v2.son)}
		<div>
			{foreach $v2.son as $v3}
			<div>
				<h2 style="cursor:pointer;"><span class="gp_contro" lv="p1-{$v1.lv1.id}|p2-{$v2.menu.id}">{$v3.display_name}</span></h2>
				<div class="divider"></div>
				<label>{$v3.display_name}<input class="gp_son" type="checkbox" name="mp_id[]" value="{$v3.id}" {if in_array($v3.id, $power)}checked{/if} lv="p1-{$v1.lv1.id}|p2-{$v2.menu.id}" /></label>
				{if isset($v3.son)}
					{foreach $v3.son as $v4}
				<label>{$v4.display_name}<input class="gp_son" type="checkbox" name="mp_id[]" value="{$v4.id}" {if in_array($v4.id, $power)}checked{/if} lv="p1-{$v1.lv1.id}|p2-{$v2.menu.id}"/></label>
					{/foreach}
				{/if}
				<br/><br/><br/>
			</div>
			{/foreach}
		</div>
		{/if}
	</div>
	{/foreach}
</div>
{/foreach}
</div>
<div class="formBar">
	<ul>
		<li><div class="buttonActive"><div class="buttonContent"><button type="submit">保存</button></div></div></li>
	</ul>
</div>
</form>

<script>
var gpAjaxDone = function (re) {
	
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

var parentCheck = function (gp_lv) {
	
	if (typeof(gp_lv)=='undefined') {
		return false;
	}

	var arr = gp_lv.split('|');

	for (const key in arr) {
		$('.'+arr[key]).find('input[type="checkbox"]').prop("checked",true);
	}
}

$('input[type="checkbox"]').bind('click', function () {

	var gp_lv = $(this).attr('lv');
	if ($(this).is(":checked")) {
		parentCheck(gp_lv);
	}
	

});

$('.gp_contro').bind('click', function () {

	if ($(this).hasClass('gpc2')) {
		var checkboxes = $(this).parent().parent().parent().parent().find('.gp_son');
		var all_checkboxes = $(this).parent().parent().parent().parent().find('input[type="checkbox"]');
	}else{
		var checkboxes = $(this).parent().parent().find('.gp_son');
		var all_checkboxes = $(this).parent().parent().find('input[type="checkbox"]');
	}
	
	var all_checked = true;//是否已全选
	checkboxes.each(function(){
		if (!$(this).is(":checked")) {
			return all_checked = false;
		}
	});
	if (all_checked) {
		checkboxes.prop("checked",false); 
		all_checkboxes.prop("checked",false); 
	}else{
		checkboxes.prop("checked",true);
		all_checkboxes.prop("checked",true);
		var gp_lv = $(this).attr('lv');
		console.log(gp_lv);
		parentCheck(gp_lv);
	}
});

var gpinit = function (class_name){
	
	var obj = $(class_name);

	obj.each(function(index1, this1){
		var checkboxes = $(this1).parent().parent().find('input[name="mp_id[]"]');
		var has_one_checked = false;//是否有任何一个被选中的
		checkboxes.each(function(index2, this2){
			if ($(this2).is(":checked")) {
				has_one_checked = true;
				return false;
			}
		});

		if (has_one_checked) {
			$(this1).find('input[type="checkbox"]').prop("checked",true); 
		}
	});
}

gpinit('.gpc2');
gpinit('.gpc1');
</script>