
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="" method="get">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					显示名称：<input type="text" name="s_name" value="" />
				</td>
				<td>
					权限标识：<input type="text" name="s_flag" value="" />
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
			<li><a class="add" href="{Fun::L('/system/manage/permission/edit')}" target="dialog" rel="system_manage_permissEdit" minable="false" width="450" height="180"><span>新增权限</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="30">序号</th>
				<th width="160">权限名称</th>
				<th width="120">权限标识</th>
				<th width="30">ID</th>
				<th width="120">操作</th>
			</tr>
		</thead>
		<tbody>
			{foreach $rows as $k=>$row}
			<tr target="sid_{$navTab}" rel="{$row.id}">
				<td>{$k+1}</td>
				<td>
					{if isset($search.lookup)&&$search.lookup==1}
					<a href="javascript:" onclick="$.bringBack({ldelim}_id:{$row.id},name:'{$row.name}',flag:'{$flag[$row.flag]}'{rdelim})">{$row.name}</a>
					{else}
					{$row.name}
					{/if}
				</td>
				<td>{$flag[$row.flag]}</td>
				<td>{$row.id}</td>
				<td>
					<a title="确实要删除？" target="ajaxTodo" href="{Fun::L('/system/manage/permission/del')}?id={$row.id}" class="btnDel">删除</a>
					<a title="编辑权限" target="dialog" href="{Fun::L('/system/manage/permission/edit')}?id={$row.id}" class="btnEdit" rel="system_manage_permissEdit"  minable="false" width="450" height="180">编辑</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form id="pagerForm" method="get" action="{$url.index.url}">
		<input type="hidden" name="pageNum" value="1" />
		<input type="hidden" name="numPerPage" value="{$page.numPerPage}" />
		<input type="hidden" name="s_name" value="{$search.s_name}" />
		<input type="hidden" name="s_flag" value="{$search.s_flag}" />
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