<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
{include file="../head.tpl"}
<script type="text/javascript">
// var detail_navtab	= "system_manage_docInfo{$doc_detail_id}";
// var detail_title	= "aaa";
// var detail_url		= "{Fun::L('/system/manage/doc/info/content')}?id={$doc_detail_id}";
{literal}
$(function(){
	DWZ.init(init.url.main+"/system/manage/jui/dwz.frag.xml", {
		statusCode:{ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
		keys: {statusCode:"statusCode", message:"message"}, //【可选】
		ui:{hideMode:'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"themes"}); // themeBase 相对于index页面的主题base路径
			// navTab.openTab(detail_navtab, detail_url, {'title': detail_title});
		}
	});
});
{/literal}
</script>
<body>
	<div id="layout">
		<div id="header">
			<div class="headerNav">
				<a class="logo" href="http://j-ui.com">标志</a>
				<ul class="nav">
					<!-- <li id="switchEnvBox"><a href="javascript:">（<span>北京</span>）切换城市</a>
						<ul>
							<li><a href="sidebar_1.html">北京</a></li>
							<li><a href="sidebar_2.html">上海</a></li>
						</ul>
					</li> -->
					<!-- <li><a href="donation.html" target="dialog" height="400" title="捐赠 & DWZ学习视频">捐赠</a></li>
					<li><a href="changepwd.html" target="dialog" rel="changepwd" width="600">设置</a></li>
					<li><a href="http://www.cnblogs.com/dwzjs" target="_blank">博客</a></li>
					<li><a href="http://weibo.com/dwzui" target="_blank">微博</a></li> -->
					<li><a href="{Fun::L('/blog/home/index')}" target="_blank">博客首页</a></li>
					<li><a href="{Fun::L('/system/manage/login/quit')}">总后台</a></li>
				</ul>
				<!-- <ul class="themeList" id="themeList">
					<li style="color:blanchedalmond;">欢迎你，{$manager.nickname}！</li>
				</ul> -->
			</div>

			<!-- navMenu -->

		</div>

		<!-- leftside -->
		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>《{$doc_title}》</h2><div>收缩</div></div>
				<div class="accordion" fillSpace="sidebar">
					<div class="accordionHeader">
						<h2><span>Folder</span>目录导航</h2>
					</div>
					<div class="accordionContent">
						{$tree_html}
					</div>
				</div>
			</div>
		</div>

		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">我的主页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">我的主页</a></li>
				</ul>
				
				<!-- 内容 -->
				<div class="navTab-panel tabsPageContent layoutBox">
					<div class="page unitBox">
					</div>
				</div>

			</div>
		</div>

	</div>

	<div id="footer">Copyright &copy; 2020 <a href="{{Fun::L('/blog/home/index')}}" target="dialog">Tec-Long</a> 京ICP备15053290号-2</div>

</body>
</html>