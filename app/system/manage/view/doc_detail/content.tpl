
<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>编辑:{$row.title}</title>
	<link rel="shortcut icon" href="{Config::C('URL')}/system/share/images/ico128.ico" type="image/x-icon" />
	<link rel="stylesheet" href="{Config::C('URL')}/vendor/twbs/bootstrap/dist/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="{Config::C('URL')}/system/share/edmd/css/editormd.css"/>
	<script src="{Config::C('URL')}/system/manage/jui/js/jquery-2.1.4.min.js"></script>
	<script src="{Config::C('URL')}/system/share/prettify/src/run_prettify.js?autoload=true&amp;skin=sunburst&amp;lang=css"></script>
	<!-- <scr ipt src="{$smarty.const.PUBLIC_TOOLS}/js/popper.min.js"></script> -->
	<script src="{Config::C('URL')}/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="{Config::C('URL')}/system/share/edmd/editormd.js"></script>
</head>
<body>
	<form class="needs-validation" action="{Fun::L('/system/manage/doc/mulu/edit/content/post')}" method="post">
		<input type="hidden" name="id" value="{$row.id}" />
		<div class="form-row d-flex mt-3">
            <div class="col-md-1"></div>
            <div class="col-md-1">
				<label><button class="btn btn-success" type="submit" style="margin-left:-110px;">应用当前操作</button></label>
			</div>
			<div class="col-md-6">
                <label style="margin-top:7px;margin-left:-110px;">目录项：{$row.title}</label>
            </div>
		</div>
		
		<div class="form-row d-flex mt-3" id="editormd">
			<textarea style="display:none;" name="content">{$row.content}</textarea>
		</div>
	</form>
	
			
<script type="text/javascript">
$(function() {
	var editor = editormd("editormd", {
		htmlDecode: "style,script,iframe",
		width: "95%",
		height:'840px',
		syncScrolling : "single",
		emoji:true,
		//启动本地图片上传功能
		imageUpload: true,
		watch:true,
		imageFormats   : ["jpg", "jpeg", "gif", "png", "bmp", "webp","zip","rar"],
		path   : "{Config::C('URL')}/system/share/edmd/lib/",
		imageUploadURL : "{Fun::L('/system/manage/editor/md/img/up')}", //文件提交请求路径
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