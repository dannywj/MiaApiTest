<?php
/**
 * Git 上线命令生成器
 * Created by DannyWang
 * wangjue@mia.com
 * 2017/1/18
 */
$weekarray = array("日", "一", "二", "三", "四", "五", "六");
//echo "星期" . $weekarray[date("w")];
$now = date('Ymd');
$today = date("w");
$yesterday = date('Ymd', strtotime('-1 day'));
if ($today == '1') {
    $yesterday = date('Ymd', strtotime('-3 day'));
}
$str_arr = array();

//$str_arr[] = "[finish merge release to master]";
$str_arr[] = "git checkout master";
$str_arr[] = "git pull";
$str_arr[] = "git tag release-" . $yesterday;
$str_arr[] = "git push origin --tag";
$str_arr[] = "git checkout -b release-" . $now;
$str_arr[] = "git push origin -u release-" . $now;
//$str_arr[] = "========== finish git release generate" . "========== ";
//var_dump($str_arr);
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 上述3个meta标签*必须*放在最前面，任何其他内容都*必须*跟随其后！ -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Mia Api Test</title>
    <script src="js/jsonFormater.js" type="text/javascript"></script>
    <link href="css/jsonFormater.css" type="text/css" rel="stylesheet"/>
    <script src="js/config.js" type="text/javascript"></script>
    <!-- Bootstrap core CSS -->
    <link href="http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        .gotoTopLink {
            position: fixed;
            left: 95%;
            bottom: 5%;
            display: block;
            height: 48px;
            width: 48px;
            background: url("images/r_top.png");
        }

        .gotoTopLinkButton {
            position: fixed;
            left: 95%;
            bottom: 15%;
            display: block;
            height: 48px;
            width: 48px;
            background: url("images/firebug.png");
        }

        .dropdown-menu {
            overflow-y: auto;
        }
    </style>
</head>

<body>
<div class="container">


    <!-- Static navbar -->
    <!-- Main component for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding-top: 4px; margin-top: 20px;">
        <h2>Mia Git Release Auto Generate</h2>
        <form class="form-inline" style="margin-bottom: 10px;">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"><span style="color: red">begin&nbsp;:</span> </span>
                <input id="txt_api" type="text" class="form-control url" placeholder="url suffix" autocomplete="off"
                       aria-describedby="basic-addon1" style="width: 300px"
                       value="[finish merge release to master]">
            </div>
        </form>

        <?php
        $html = '';
        $i = 0;
        foreach ($str_arr as $item) {
            $step = $i + 1;
            $html .= "
                <form class=\"form-inline\" style=\"margin-bottom: 10px;\">
            <div class=\"input-group\">
                <span class=\"input-group-addon\" id=\"basic-addon1\"><span style=\"color: red\">step-{$step}:</span> </span>
                <input id=\"txt_api\" type=\"text\" class=\"form-control url\" placeholder=\"url suffix\" autocomplete=\"off\"
                       aria-describedby=\"basic-addon1\" style=\"width: 300px\"
                       value=\"{$item}\">
            </div>
            <div class=\"btn-group\" role=\"group\" aria-label=\"...\">
                <button id=\"btn_{$i}\" data-clipboard-text=\"{$item}\" type=\"button\" class=\"btn btn-info btn_platform\" value=\"android\">copy</button>
            </div>
            <span id='span_btn_{$i}' style='color: red; display: none;'>copied!</span>
        </form>";
            $i++;
        }
        echo $html;
        ?>

        <form class="form-inline" style="margin-bottom: 10px;">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1"><span
                        style="color: red">end&nbsp;&nbsp;&nbsp;&nbsp;:</span> </span>
                <input id="txt_api" type="text" class="form-control url" placeholder="url suffix" autocomplete="off"
                       aria-describedby="basic-addon1" style="width: 300px"
                       value="=== finish git release generate ===">
            </div>
        </form>
    </div>

</div> <!-- /container -->

<!-- Bootstrap core JavaScript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
<script src="http://cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="http://zeroclipboard.org/javascripts/zc/v2.2.0/ZeroClipboard.js"></script>
<script>


    function copy(id) {
        //alert(id);
        var client = new ZeroClipboard(document.getElementById(id));
        client.on("ready", function (readyEvent) {
            // alert( "ZeroClipboard SWF is ready!" );
            client.on("aftercopy", function (event) {
                // `this` === `client`
                // `event.target` === the element that was clicked
                //event.target.style.display = "none";
                //alert("Copied text to clipboard: " + event.data["text/plain"]);

                $("#span_" + id).show();
                $("#" + id).hide();
            });
        });
    }

    <?php
    $i = 0;
    foreach ($str_arr as $item) {
        echo "copy('btn_$i');   ";
        $i++;
    }
    ?>
</script>
</body>
</html>

