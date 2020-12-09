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
                    <li><a class="add" href="" target="dialog" mask="true"><span>新文档</span></a></li>
                    <li><a class="edit" href="" target="dialog" mask="true"><span>修改文档标题</span></a></li>
                    <li class="line">line</li>
                    <li><a class="icon" href="demo/common/dwz-team.xls" target="dwzExport" title="实要导出这些记录吗?"><span>导出EXCEL</span></a></li>
                    <li><a class="delete" href="" target="ajaxTodo" title="确定要删除吗?"><span>放入回收站</span></a></li>
                </ul>
            </div>
            <table class="table" width="99%" layoutH="138" rel="jbsxBox2">
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
                <div class="pages">
                    <span>显示</span>
                    <span>条，总共{$page.totalNum}条记录，合计{$page.totalPageNum}页</span>
                </div>
        
                <div class="pagination" targetType="navTab" totalCount="{$page.totalNum}" numPerPage="{$page.numPerPage}" pageNumShown="10" currentPage="{$page.pageNum}"></div>
        
            </div>
        </div>
	</div>
	
	<div id="system_manage_docMuluList" class="unitBox" style="margin-left:520px;">
	</div>
	
</div>
