
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>添加工程信息</title>
	<link rel="shortcut icon" href="{Config::C('URL')}/system/share/images/ico128.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{$smarty.const.BOOTSTRAP4}/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="{$smarty.const.PUBLIC_TOOLS}/edmd/css/editormd.css"/>
	<!-- <script src="{$smarty.const.PUBLIC_TOOLS}/js/jquery-3.3.1.slim.min.js"></script> -->
	<script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery-2.1.4.min.js"></script>
	<script src="{$smarty.const.PUBLIC_TOOLS_PRETTIFY}/src/run_prettify.js?autoload=true&amp;skin=sunburst&amp;lang=css"></script>
	<script src="{$smarty.const.PUBLIC_TOOLS}/js/popper.min.js"></script>
	<script src="{$smarty.const.BOOTSTRAP4}/js/bootstrap.min.js"></script>
	<script src="{$smarty.const.PUBLIC_TOOLS}/editor_md/editormd.js"></script>
</head>
<body>
	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a class="nav-link active">添加工程信息</a>
		</li>
	</ul>
	<form class="needs-validation" action="{$url.adh.url}" method="post">
		<div class="form-row d-flex mt-3">
			<div class="col-md-1"></div>
			<div class="col-md-2">
				<label for="tools_prorecord_add_title">工程信息标题</label>
				<input type="text" class="form-control" id="tools_prorecord_add_title" name="title" placeholder="必填项" value="" required>
			</div>
			<div class="col-md-1">
				<label>所属工程</label>
				<select name="belong_pro" class="form-control">
					{foreach $belong_pro as $belong_pro_key=>$belong_pro_val}
					<option value="{$belong_pro_key}">{$belong_pro_val}</option>
					{/foreach}
				</select>
			</div>
			<div class="col-md-1">
				<label> </label>
				<button class="btn btn-success" type="submit" style="margin-top:32px;">立即添加</button>
			</div>
		</div>
		
		<div class="form-row d-flex mt-3" id="editormd">
			<textarea style="display:none;" name="content"></textarea>
		</div>
	</form>
	
			
<script type="text/javascript">
$(function() {
	var editor = editormd("editormd", {
		htmlDecode: "style,script,iframe",
		width: "95%",
		height:'640px',
		syncScrolling : "single",
		emoji:true,
		//启动本地图片上传功能
		imageUpload: true,
		watch:true,
		imageFormats   : ["jpg", "jpeg", "gif", "png", "bmp", "webp","zip","rar"],
		path   : "{$smarty.const.PUBLIC_TOOLS}/edmd/lib/",
		imageUploadURL : "{$url.imgupmd.url}", //文件提交请求路径
		saveHTMLToTextarea : true, //注意3：这个配置，方便post提交表单
		theme        : "default",
		// Preview container theme, added v1.5.0
		// You can also custom css class .editormd-preview-theme-xxxx
		previewTheme : "default", 
		// Added @v1.5.0 & after version is CodeMirror (editor area) theme
		editorTheme  : "blackboard", 
	});
});
// 			/*
//  上传的后台只需要返回一个 JSON 数据，结构如下：
//  {
// success : 0 | 1,           // 0 表示上传失败，1 表示上传成功
// message : "提示的信息，上传成功或上传失败及错误信息等。",
// url     : "图片地址"        // 上传成功时才返回
//  }
// 			 */
//testEditor.getMarkdown();       // 获取 Markdown 源码
//testEditor.getHTML();           // 获取 Textarea 保存的 HTML 源码
//testEditor.getPreviewedHTML();  // 获取预览窗口里的 HTML，在开启 watch 且没有开启 saveHTMLToTextarea 时使用
</script>
</body>
</html>