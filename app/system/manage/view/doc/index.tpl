<div class="pageContent">
	<div id="{$navTab}_doc" class="unitBox" style="float:left; display:block; overflow:auto; width:514px;">
		<div class="pageHeader" style="border:1px #B8D0D6 solid">
			<form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBox2');" action="demo/pagination/list2.html" method="post">
				<input type="hidden" name="pageNum" value="1" />
				<input type="hidden" name="numPerPage" value="" />
				<input type="hidden" name="orderField" value="" />
				<div class="searchBar">
					<table class="searchContent">
						<tr>
							<td>
								姓名：<input type="text" name="name" />
							</td>
							<td><div class="buttonActive"><div class="buttonContent"><button type="submit">检索</button></div></div></td>
						</tr>
					</table>
				</div>
			</form>
        </div>
        <div class="pageContent" style="border-left:1px #B8D0D6 solid;border-right:1px #B8D0D6 solid">
            <div class="panelBar">
                <ul class="toolBar">
                    <li><a class="add" href="{Fun::L('/system/manage/doc/edit')}" target="dialog" mask="true"><span>新文档</span></a></li>
                    <li><a class="edit" href="{Fun::L('/system/manage/doc/edit')}?id={ldelim}sid_{$navTab}}" target="dialog" mask="true"><span>修改文档标题</span></a></li>
                    <li class="line">line</li>
                    <li><a class="icon" href="demo/common/dwz-team.xls" target="dwzExport" title="实要导出这些记录吗?"><span>导出EXCEL</span></a></li>
                    <li><a class="delete" href="" target="ajaxTodo" title="确定要删除吗?"><span>放入回收站</span></a></li>
                </ul>
            </div>
            <table class="table" width="99%" layoutH="138" rel="jbsxBox2">
                <thead>
                    <tr>
                        <th width="30">序号</th>
                        <th>标题</th>
                        <th>创建时间</th>
                        <th>最后修改时间</th>
                        <th>ID</th>
                        <!-- <th>操作</th> -->
                    </tr>
                </thead>
                <tbody>
                    {foreach $rows as $k=>$row}
                    <tr target="sid_{$navTab}" rel="{$row.id}">
                        <td>{$k+1}</td>
                        <td>
                            <a href="{Fun::L('/system/manage/doc/mulu/list')}?id={$row.id}" target="ajax" rel="system_manage_docMuluList" style="font-size:medium; text-decoration:none;">
								{$row.title}
							</a>
                        </td>
                        
                        <td>{date('Y-m-d H:i:s', $row.created_time)}</td>
                        <td>
                            {if !empty($row['update_time'])}
                            {date('Y-m-d H:i:s', $row.update_time)}
                            {/if}
                        </td>
                        <td>{$row.id}</td>
                        <!-- <td>
                            <a title="编辑文档" target="navTab" href="{Fun::L('/system/manage/doc/edit')}?id={$row.id}" class="btnEdit" rel="system_manage_docEdit">编辑文档</a>
                        </td> -->
                    </tr>
                    {/foreach}
                </tbody>
            </table>
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
	</div>
	
	<div id="system_manage_docMuluList" class="unitBox" style="margin-left:520px;">
	</div>
	
</div>
