
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="" method="get" onreset="$(this).find('select.combox').comboxReset()">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					标题：<input type="text" name="s_name" value="" />
				</td>
			</tr>
		</table>
		<div class="subBar">
			<ul>
				<li><div class="button"><div class="buttonContent"><button type="reset">重置</button></div></div></li>
				<li><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></li>
				<li><a class="button" href="" target="dialog" mask="true" title="查询框"><span>高级检索</span></a></li>
			</ul>
		</div>
	</div>
	</form>
</div>
<div class="pageContent">
	<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="{Fun::L('/system/manage/doc/edit')}" target="dialog" rel="system_manage_docEdit" minable="false" width="450" height="280"><span>新文档 </span></a></li>
			<li><a class="edit" href="{Fun::L('/system/manage/doc/edit')}?id={ldelim}sid_{$navTab}}" target="dialog"  rel="system_manage_docEdit"><span>修改文档标题</span></a></li>
			<li><a class="delete" href="" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="30">序号</th>
				<th width="200">标题</th>
				<th width="120">创建时间</th>
				<th width="120">最后修改时间</th>
				<th width="30">ID</th>
				<th width="160">操作</th>
			</tr>
		</thead>
		<tbody>
			{foreach $rows as $k=>$row}
			<tr target="sid_{$navTab}" rel="{$row.id}">
				<td>{$k+1}</td>
				<td>{$row.title}</td>
				<td>{date('Y-m-d H:i:s', $row.created_time)}</td>
				<td>
					{if !empty($row['update_time'])}
					date('Y-m-d H:i:s', $row.update_time)
					{/if}
				</td>
				<td>{$row.id}</td>
				<td>
					<a title="编辑文档" target="navTab" href="{Fun::L('/system/manage/doc/edit')}?id={$row.id}" class="btnEdit" rel="system_manage_docEdit">编辑文档</a>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form id="pagerForm" method="get" action="{Fun::L('/system/manage/doc/list')}">
		<input type="hidden" name="pageNum" value="1" />
		<input type="hidden" name="numPerPage" value="{$page.numPerPage}" />
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