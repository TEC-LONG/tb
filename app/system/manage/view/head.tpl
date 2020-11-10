<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>fires工具管理系统</title>
    <link rel="shortcut icon" href="{$smarty.const.PUBLIC_TOOLS}/image/ico.ico" type="image/x-icon" />
    <link href="{$smarty.const.PUBLIC_TOOLS_JUI}/themes/default/style.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="{$smarty.const.PUBLIC_TOOLS_JUI}/themes/css/core.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="{$smarty.const.PUBLIC_TOOLS_JUI}/themes/css/print.css" rel="stylesheet" type="text/css" media="print"/>
    <link href="{$smarty.const.PUBLIC_TOOLS_JUI}/uploadify/css/uploadify.css" rel="stylesheet" type="text/css" media="screen"/>
    <!--[if IE]>
    <link href="{$smarty.const.PUBLIC_TOOLS_JUI}/themes/css/ieHack.css" rel="stylesheet" type="text/css" media="screen"/>
    <![endif]-->
    
    <!--[if lt IE 9]><script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/speedup.js" type="text/javascript"></script><script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery-1.11.3.min.js" type="text/javascript"></script><![endif]-->
    <!--[if gte IE 9]><!--><script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery-2.1.4.min.js" type="text/javascript"></script><!--<![endif]-->
    
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery.cookie.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery.validate.js" type="text/javascript"></script>
    <!--<script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/jquery.bgiframe.js" type="text/javascript"></script>-->
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/xheditor/xheditor-1.2.2.min.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/xheditor/xheditor_lang/zh-cn.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/uploadify/scripts/jquery.uploadify.js" type="text/javascript"></script>
    
    <script type="text/javascript" src="{$smarty.const.PUBLIC_TOOLS_JUI}/chart/echarts.min.js"></script>
    <!-- <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=6PYkS1eDz5pMnyfO0jvBNE0F"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/library/Heatmap/2.0/src/Heatmap_min.js"></script> -->
    
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.core.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.util.date.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.validate.method.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.barDrag.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.drag.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.tree.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.accordion.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.ui.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.theme.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.switchEnv.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.alertMsg.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.contextmenu.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.navTab.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.tab.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.resize.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.dialog.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.dialogDrag.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.sortDrag.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.cssTable.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.stable.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.taskBar.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.ajax.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.pagination.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.database.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.datepicker.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.effects.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.panel.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.checkbox.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.history.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.combox.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.file.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.print.js" type="text/javascript"></script>
    
    <!-- 可以用dwz.min.js替换前面全部dwz.*.js (注意：替换时下面dwz.regional.zh.js还需要引入)
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/bin/dwz.min.js" type="text/javascript"></script>
    -->
    <script src="{$smarty.const.PUBLIC_TOOLS_JUI}/js/dwz.regional.zh.js" type="text/javascript"></script>
    <script src="{$smarty.const.PUBLIC_TOOLS}/init.conf.js" type="text/javascript"></script>
    
    
    <script type="text/javascript">
    var twice_login_url = "{L('/tools/login/check')}";
    {literal}
    $(function(){
        DWZ.init(init.url.main+"/public/tools/jui/new/dwz.frag.xml", {
            loginUrl:twice_login_url, loginTitle:"登录",	// 弹出登录对话框
    //		loginUrl:"login.html",	// 跳到登录页面
            statusCode:{ok:200, error:300, timeout:301}, //【可选】
            pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"}, //【可选】
            keys: {statusCode:"statusCode", message:"message"}, //【可选】
            ui:{hideMode:'offsets'}, //【可选】hideMode:navTab组件切换的隐藏方式，支持的值有’display’，’offsets’负数偏移位置的值，默认值为’display’
            debug:true,	// 调试模式 【true|false】
            callback:function(){
                initEnv();
                $("#themeList").theme({themeBase:"themes"}); // themeBase 相对于index页面的主题base路径
            }
        });
    });
    {/literal}
    </script>
    
    </head>