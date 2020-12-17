<div class="pageHeader" style="border:1px #B8D0D6 solid">
	<div class="searchBar">
		<table class="searchContent">
			<tr>
				<td>
                    <a href="{Fun::L('/system/manage/doc/info')}?id={$doc__id}" target="_blank"><span style="font-size:medium">《{$doc_title}》</span></a>
                </td>
			</tr>
		</table>
	</div>
</div>

<div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
<div class="panelBar">
		<ul class="toolBar">
			<li><a class="add" href="{Fun::L('/system/manage/doc/mulu/edit')}?doc__id={$doc__id}" target="dialog" mask="true" minable="false" width="450" height="180" rel="system_manage_docMuluEdit"><span>新建目录项</span></a></li>
			<li><a class="delete" href="demo/pagination/ajaxDone3.html?uid=" target="ajaxTodo" title="确定要删除吗?"><span>删除目录项</span></a></li>
			<li><a class="edit" href="{Fun::L('/system/manage/doc/mulu/edit')}?doc__id={$doc__id}&id={ldelim}sid_system_manage_docMuluList_id}" target="dialog" mask="true" minable="false" width="450" height="180" rel="system_manage_docMuluEdit"><span>修改目录项</span></a></li>
		</ul>
	</div>
	<table class="table" width="99%" layoutH="138">
		<thead>
			<tr>
				<th></th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			{$tree_html}
		</tbody>
	</table>
	<div class="panelBar">
	</div>
</div>