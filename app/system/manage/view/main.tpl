<!-- container -->

<div class="navTab-panel tabsPageContent layoutBox">
	<div class="page unitBox">

		<h2 class="subTitle">常用链接</h2>
		{foreach $nav_link as $k=>$v}
		<div class="row" style="padding: 0 10px;">
			{foreach $v as $k1=>$v1}
			<div class="col-md-1 col-sm-12">
				<a href="{$v1}" target="_blank">{$k1}</a>
			</div>
			{/foreach}
		</div>
		{/foreach}
		<!-- <div class="row" style="padding: 0 10px;">
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test1
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00;">
				<p>test2</p>
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test3
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test4
			</div>
		</div>
		<div class="row" style="padding: 0 10px;">
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test1
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00;">
				<p>test2</p>
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test3
			</div>
			<div class="col-md-3 col-sm-12" style="border: 1px dashed #f00">
				test4
			</div>
		</div> -->

		<div class="row">
<style>
* {
	margin: 0;
	padding: 0;
	list-style: none;
	border: none;
}
#zzsc {
	width: 920px;
	margin: 100px auto;
}
</style>
<script type="text/javascript" src="{$smarty.const.PUBLIC_TOOLS}/zzsc.js"></script>
			<div class="col-md-12 col-sm-12">
				<div id="zzsc">
					<canvas id="canvas" width="920" height="200"></canvas>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- <div class="navTab-panel tabsPageContent layoutBox">
	<div class="page unitBox">

		<div class="accountInfo">
			<div class="alertInfo">
				<h2>开发规范</h2>
				<a href="http://note.youdao.com/noteshare?id=2c21d089d796966860e7f6b4a9999040" target="_blank">A44团队</a>
				<a href="#" target="_blank">TP规范</a>
			</div>
			<div class="right">
				<p><a href="#" target="_blank" style="line-height:19px">sina微博</a></p>
				<p><a href="#" target="_blank" style="line-height:19px">tencent微博</a></p>
			</div>
			<p><span>博客</span></p>
			<p><a href="#" target="_blank">fires.wang(blog)</a></p>
		</div>

		<div class="pageFormContent" layoutH="120" style="margin-right:230px">
			<div class="divider"></div>
			<div class="sortDrag" style="width:32%;border:1px solid #e66;margin:5px;float:left;min-height:100px">
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">
					<a href="xxxx" target="_blank">管理系统 【<font style="color:green;">★★★★★</font>】</a>
				</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">
					<a href="xxxx" target="_blank">个人博客系统 【<font style="color:red;">★★★★★</font>】</a>
				</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px"><a href="#" target="_blank">商城首页 【<font style="color:red;">★★★★★</font>】</a></div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px"><a href="#" target="_blank">商城个人中心 【<font style="color:red;">★★★★★</font>】</a></div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">A5</div>
			</div>
			<div class="sortDrag" style="width:32%;border:1px solid #e66;margin:5px;float:left;min-height:100px">
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B1</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B2</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B3</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B4</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B5</div>
			</div>
			<div class="sortDrag" style="width:32%;border:1px solid #e66;margin:5px;float:left;min-height:100px">
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B1</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B2</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B3</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B4</div>
				<div style="border:1px solid #B8D0D6;padding:5px;margin:5px">B5</div>
			</div>
			<div class="divider"></div>
			<h2>常用站点:</h2>
			<p>
				<a href="http://www.baidu.com" target="_blank">百度搜索</a>&nbsp;&nbsp;
				<a href="http://pan.baidu.com" target="_blank">百度网盘</a>&nbsp;&nbsp;
				<a href="http://zhanzhang.baidu.com" target="_blank">百度站长平台</a>&nbsp;&nbsp;
				<a href="http://tongji.baidu.com/web/welcome/login" target="_blank">百度统计</a>&nbsp;&nbsp;
				<a href="https://mtj.baidu.com/web/welcome/login" target="_blank">百度移动统计</a>&nbsp;&nbsp;
			</p>
		</div>

	</div>
</div> -->
