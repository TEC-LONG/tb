<!doctype html>
<html lang="en">

<head>
<title>Brego | Login</title>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, initial-scale=1, shrink-to-fit=no">
<meta name="description" content="Brego Bootstrap 4x admin is super flexible, powerful, clean &amp; modern responsive admin dashboard with unlimited possibilities.">
<meta name="author" content="GetBootstrap, design by: puffintheme.com">

<link rel="icon" href="favicon.png" type="image/x-icon">
<!-- VENDOR CSS -->
<link rel="stylesheet" href="{Config::C('URL')}/system/share/vendor/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="{Config::C('URL')}/system/share/css/font-awesome.4.7.0.css">
<!-- <link rel="stylesheet" href="https://www.jq22.com/jquery/font-awesome.4.7.0.css"> -->
<link rel="stylesheet" href="{Config::C('URL')}/system/share/vendor/animate-css/vivify.min.css">

<!-- MAIN CSS -->
<link rel="stylesheet" href="{Config::C('URL')}/system/share/css/site.css">

</head>

<body class="theme-blush">

    <div class="auth-main particles_js">
        <div class="auth_div vivify popIn">
            <div class="auth_brand">
                <a class="navbar-brand" href="#"><img src="{Config::C('URL')}/system/share/images/logo1.png" /></a>
            </div>
            <div class="card">
                <div class="pattern">
                    <span class="red"></span>
                    <span class="indigo"></span>
                    <span class="blue"></span>
                    <span class="green"></span>
                    <span class="orange"></span>
                </div>
                <div class="header">
                    <p class="lead">XiTong HouTai</p>
                </div>
                <div class="body">
                    <form class="form-auth-small" action="index.html">
                        <div class="form-group">
                            <label for="signin-email" class="control-label sr-only">Email</label>
                            <input name="acc" type="email" class="form-control round" id="signin-email" value="" placeholder="YouXiang">
                        </div>
                        <div class="form-group">
                            <label for="signin-password" class="control-label sr-only">Password</label>
                            <input name="pwd" type="password" class="form-control round" id="signin-password" value="" placeholder="MiMa">
                        </div>
                        <div class="form-group clearfix">
                            <label class="fancy-checkbox element-left">
                                <input type="checkbox">
                                <span>JiZhu Wo</span>
                            </label>								
                        </div>
                        <button type="submit" class="btn btn-primary btn-round btn-block">DENG</button>
                        <div class="bottom">
                            <span class="helper-text m-b-10"><i class="fa fa-lock"></i> <a href="page-forgot-password.html">ZhaoHui MiMa?</a></span>
                            <!-- <span>没有账号? <a href="page-register.html">ShenQing ZhangHao</a></span> -->
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="particles-js"></div>
    </div>
    <!-- END WRAPPER -->
    
<script src="{Config::C('URL')}/system/share/bundles/libscripts.bundle.js"></script>    
<script src="{Config::C('URL')}/system/share/bundles/vendorscripts.bundle.js"></script>

<script src="{Config::C('URL')}/system/share/vendor/particlesjs/particles.min.js"></script>
<script src="{Config::C('URL')}/system/share/bundles/mainscripts.bundle.js"></script>
<script src="{Config::C('URL')}/system/share/js/pages/particlesjs.js"></script>
</body>
</html>
