<div class="pageHeader" style="border:1px #B8D0D6 solid">
	<form id="pagerForm" onsubmit="return divSearch(this, 'jbsxBox3');" action="demo/pagination/list3.html" method="post">
	<input type="hidden" name="pageNum" value="1" />
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

<h2 class="contentTitle">树形菜单</h2>

<div id="resultBox"></div>

<div style=" float:left; display:block; margin:10px; overflow:auto; width:100%; height:770px; border:solid 1px #CCC; line-height:21px; background:#FFF;">
    {$tree_html}
    <!-- <ul class="tree">
        <li><a href="http://www.baidu.com" target="_blank">框架面板</a>
            <ul>
                <li><a href="http://www.baidu.com" target="_blank">我的主页</a>
                    <ul>
                        <li><a href="http://www.baidu.com" target="_blank">我的主页</a></li>
                        <li><a href="newPage1.html" target="navTab" rel="page1">页面一</a></li>
                        <li><a href="newPage2.html" target="navTab" rel="page1">替换页面一</a></li>
                        <li><a href="newPage2.html" target="navTab" rel="page2">页面二</a></li>
                        <li><a href="newPage3.html" target="navTab" rel="page3" title="页面三（自定义标签名）">页面三</a></li>
                    </ul>
                </li>
                <li><a href="newPage1.html" target="navTab" rel="page1">页面一</a></li>
                <li><a href="newPage2.html" target="navTab" rel="page1">替换页面一</a></li>
                <li><a href="newPage2.html" target="navTab" rel="page2">页面二</a></li>
                <li><a href="newPage3.html" target="navTab" rel="page3" title="页面三（自定义标签名）">页面三</a></li>
            </ul>
        </li>
        <li><a href="w_panel.html" target="navTab" rel="w_panel">面板</a></li>
        <li><a href="w_tabs.html" target="navTab" rel="w_tabs">选项卡面板</a></li>
        <li><a href="w_dialog.html" target="navTab" rel="w_dialog">弹出窗口</a></li>
        <li><a href="w_alert.html" target="navTab" rel="w_alert">提示窗口</a></li>
        <li><a href="w_table.html" target="navTab" rel="w_table">表格容器</a></li>
        <li><a href="w_tree.html" target="navTab" rel="w_tree">树形菜单</a></li>
        <li><a href="w_editor.html" target="navTab" rel="w_editor">编辑器</a></li>
        
    </ul> -->
</div>


