<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
        {if isset($title)}
        {$title}
        {else}
        
        {/if}
    </title>
	<link rel="shortcut icon" href="{Config::C('URL')}/system/share/images/ico128.ico" type="image/x-icon" />
    <link href="{Config::C('URL')}/system/manage/jui/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="{Config::C('URL')}/system/manage/jui/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="{Config::C('URL')}/system/manage/jui/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
    <link href="{Config::C('URL')}/system/manage/jui/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
    <!--[if IE]>
    <link href="{Config::C('URL')}/system/manage/jui/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
    <![endif]-->
    
    <!--[if lt IE 9]><script src="{Config::C('URL')}/system/manage/jui/js/speedup.js" type="text/javascript"></script><script src="{Config::C('URL')}/system/manage/jui/js/jquery-1.11.3.min.js" type="text/javascript"></script><![endif]-->
    <!--[if gte IE 9]><!--><script src="{Config::C('URL')}/system/manage/jui/js/jquery-2.1.4.min.js" type="text/javascript"></script><!--<![endif]-->
    
    <script src="{Config::C('URL')}/system/manage/jui/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/jquery.validate.js" type="text/javascript"></script>
    <!--<script src="{Config::C('URL')}/system/manage/jui/js/jquery.bgiframe.js" type="text/javascript"></script>-->
    <script src="{Config::C('URL')}/system/manage/jui/xheditor/xheditor-1.2.2.min.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>
    
    <script type="text/javascript" src="{Config::C('URL')}/system/manage/jui/chart/echarts.min.js"></script>
    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=6PYkS1eDz5pMnyfO0jvBNE0F"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/Heatmap/2.0/src/Heatmap_min.js"></script> -->
    
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.core.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.util.date.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.validate.method.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.barDrag.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.drag.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.tree.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.accordion.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.ui.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.theme.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.switchEnv.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.alertMsg.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.contextmenu.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.navTab.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.tab.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.resize.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.dialog.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.dialogDrag.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.sortDrag.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.cssTable.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.stable.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.taskBar.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.ajax.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.pagination.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.database.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.datepicker.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.effects.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.panel.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.checkbox.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.history.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.combox.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.file.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.print.js" type="text/javascript"></script>
    
    <!-- 可以用dwz.min.js替换前面全部dwz.*.js (注意：替换时下面dwz.regional.zh.js还需要引入)
    <script src="{Config::C('URL')}/system/manage/jui/bin/dwz.min.js" type="text/javascript"></script>
    -->
    <script src="{Config::C('URL')}/system/manage/jui/js/dwz.regional.zh.js" type="text/javascript"></script>
    <script src="{Config::C('URL')}/system/manage/init.conf.js" type="text/javascript"></script>
    
    </head>