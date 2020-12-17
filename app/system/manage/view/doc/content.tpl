<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{$row.title}</title>
    <link rel="shortcut icon" href="{Config::C('URL')}/system/share/images/ico128.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{Config::C('URL')}/vendor/twbs/bootstrap/dist/css/bootstrap.min.css"/>
    <script src="{Config::C('URL')}/system/manage/jui/js/jquery-2.1.4.min.js"></script>
    <script src="{Config::C('URL')}/vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{Config::C('URL')}/system/share/css/prism.css"/>
    <script src="{Config::C('URL')}/system/share/js/prism.js"></script>
    <link rel="stylesheet" href="{Config::C('URL')}/system/share/css/jquery.autoMenu.css">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <blockquote class="blockquote text-right">
                    <p class="mb-0">【{$this_doc_detail.title}】 {date('Y-m-d H:i:s', $this_doc_detail['created_time'])}</p>
                    <footer class="blockquote-footer">
                        <cite title="Source Title">《{$ptitle}》</cite>
                    </footer>
                </blockquote>
            </div>
            <div class="col-sm-1"></div>
            
        </div>
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm line-numbers content">
                {htmlspecialchars_decode($this_doc_detail.content_html)}
            </div>
            <div class="col-sm-1"></div>
          </div>
    </div>
    <div class="autoMenu" id="autoMenu" data-autoMenu> </div>
<script src="{Config::C('URL')}/system/share/js/jquery.autoMenu.js"></script> 
<script>
var content = $('.content');
var table = content.find('table');

var setHn = function (tag, style, level_flag) {

    content.find(tag).each(function(index, elem){

        if (tag=='h2') {
            var this_new_html = '<button type="button" class="btn btn-outline-warning"><span class="badge '+style+'"> '+level_flag+' '+$(elem).html()+'</span></button>';
        }else{
            var this_new_html = '<span class="badge '+style+'"> '+level_flag+' '+$(elem).html()+'</span>';
        }
        
        $(elem).html(this_new_html);
    });
};

$(function(){

        //$('#autoMenu').autoMenu();

        content.children().attr('style', 'margin-top:12px;');
        // content.find('ol li').addClass('lead');
        content.find('ol').addClass('list-group list-group-flush');
        content.find('ol li').addClass('list-group-item');
        content.find('ul').addClass('list-group list-group-flush');
        content.find('ul li').addClass('list-group-item');
        content.find('p').addClass('alert-success');

        setHn('h1', 'badge-info', '|-');
        setHn('h2', 'badge-warning', '||-');
        setHn('h3', 'badge-success', '|||-');
        setHn('h4', 'badge-danger', '||||-');

        table.addClass('table table-bordered table-hover table-striped table-dark');
        table.find('thead').addClass('bg-primary');
})
</script>
</body>
</html>