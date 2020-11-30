
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="" method="post">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					组名：<input type="text" name="s_name" value="" />
				</td>
				<td>
					<div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div>
				</td>
			</tr>
		</table>
		<div class="subBar">
			<ul>
				<li></li>
			</ul>
		</div>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="{Fun::L('/system/manage/user/group/edit')}" target="dialog" rel="system_manage_userGroupEdit" minable="false" width="750" height="240"><span>新增用户组</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="30">序号</th>
				{foreach $thead as $col}
				<th {if !empty($col.width)}width="{$col.width}"{/if}>{$col.ch}</th>
				{/foreach}
				<th width="160">组名</th>
				<th width="120">排序</th>
				<th width="30">ID</th>
				<th width="120">操作</th>
			</tr>
		</thead>
		<tbody>
			{foreach $rows as $k=>$row}
			<tr target="sid_{$navTab}" rel="{$row.id}">
				<td>{$k+1}</td>
				<td>{$row.name}</td>
				<td>{$row.sort}</td>
				<td>{$row.id}</td>
				<td>
					<a title="确实要删除？" target="ajaxTodo" href="" class="btnDel">删除</a>
					<a title="编辑用户组" target="dialog" href="{Fun::L('/system/manage/user/group/edit')}?id={$row.id}" class="btnEdit" rel="system_manage_userGroupEdit"  minable="false" width="650" height="440">编辑用户组</a>
					<a title="设置用户组权限" target="dialog" href="" class="btnAssign" rel=""  minable="false" width="950" height="740">设置用户组权限</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form id="pagerForm" method="get" action="{Fun::L('/system/manage/user/group')}">
		<input type="hidden" name="pageNum" value="1" />
		<input type="hidden" name="numPerPage" value="{$page.numPerPage}" />
		<input type="hidden" name="s_name" value="{$search.s_name}" />
		<!-- <input type="hidden" name="s_mem_acc" value="{$search.s_mem_acc}" />
		<input type="hidden" name="s_mem_pwd" value="{$search.s_mem_pwd}" /> -->
	</form>
	<div class="panelBar">
		<div class="pages">
			<span>显示</span>
			<select class="combox" name="numPerPage" {literal}onchange="navTabPageBreak({numPerPage:this.value})"{/literal}>
				<option value="{$page.numPerPage}">{$page.numPerPage}</option>
				{foreach $page.numPerPageList as $thisNumPerPage}
					{if $thisNumPerPage!=$page.numPerPage}
				<option value="{$thisNumPerPage}">{$thisNumPerPage}</option>
					{/if}
				{/foreach}
			</select>
			<span>条，总共{$page.totalNum}条记录，合计{$page.totalPageNum}页</span>
		</div>

		<div class="pagination" targetType="navTab" totalCount="{$page.totalNum}" numPerPage="{$page.numPerPage}" pageNumShown="10" currentPage="{$page.pageNum}"></div>

	</div>
</div>