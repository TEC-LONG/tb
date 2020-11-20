
<div class="pageHeader">
	<form onsubmit="return navTabSearch(this);" action="" method="get" onreset="$(this).find('select.combox').comboxReset()">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
					账号：<input type="text" name="acc" value="" />
				</td>
				<td>
					用户昵称：<input type="text" name="nickname" value="" />
				</td>
				<!-- <td class="dateRange">
					建档日期:
					<input name="startDate" class="date readonly" readonly="readonly" type="text" value="">
					<span class="limit">-</span>
					<input name="endDate" class="date readonly" readonly="readonly" type="text" value="">
				</td> -->
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
			<li><a class="add" href="" target="navTab" rel=""><span>添加管理员</span></a></li>
			<li><a class="delete" href="" target="ajaxTodo" title="确定要删除吗?"><span>删除</span></a></li>
			<li><a class="edit" href="" target="navTab"  rel=""><span>修改管理员信息</span></a></li>
			<li class="line">line</li>
			<li><a class="icon" href="demo/common/dwz-team.xls" target="dwzExport" targetType="navTab" title="实要导出这些记录吗?"><span>导出EXCEL</span></a></li>
		</ul>
	</div>
	<table class="table" width="100%" layoutH="138">
		<thead>
			<tr>
				<th width="30"><input type="checkbox" group="ids" class="checkboxCtrl"></th>
				<th width="30">序号</th>
				<th width="30">ID</th>
				<th width="120">账号</th>
				<th width="120">昵称</th>
				<th width="100">手机号</th>
				<th width="160">邮箱</th>
				<th width="60">用户级别</th>
				<th width="100">状态</th>
				<th width="120">新增来源</th>
				<th width="160">所属组</th>
			</tr>
		</thead>
		<tbody>
			{foreach $rows as $k=>$row}
			<tr target="sid_{$navTab}" rel="{$row.id}">
				<td><input name="ids" value="{$row.id}" type="checkbox"></td>
				<td>{$k+1}</td>
				<td>{$row.id}</td>
				<td>{$row.acc}</td>
				<td>{$row.nickname}</td>
				<td>{$row.cell}</td>
				<td>{$row.email}</td>
				<td>{$level[$row[$field]]}</td>
				<td>{$status[$row[$field]]}</td>
				<td>{$ori[$row[$field]]}</td>
				<td>{$row.gname}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>

	<form id="pagerForm" method="get" action="{Fun::L('/system/manage/user/list')}">
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