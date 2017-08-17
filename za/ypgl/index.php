<?php 
@$startId = $_GET['startId'] ?: '';
@$endId = $_GET['endId'] ?: '';
?><head>
<meta charset="utf-8">
</head>
<html>

<div style="height: 5%">
    <form>
        ID：<input name="startId" value="<?=$startId?>"> TO <input name="endId" value="<?=$endId?>">
        <button>搜索</button>
    </form>
</div>

<div style="height: 95%">
    <iframe id="fanhe" src="http://test.glance.admin.ouj.com/commentary/waitList?type=-1&displayIdStart=<?=$startId?>&displayIdEnd=<?=$endId?>&title=&vid=&upId=" style="width: 73%; height: 100%; float: left;"></iframe>
    <div style="width: 26%; height: 100%; float: left;">
        <iframe id="tool" src="http://<?=$_SERVER['HTTP_HOST']?>/tool.html" style="width:100%; height: 25%; clear: both;"></iframe>
        <iframe id="upimg" src="http://ceshi.duowan.com/upimg/index.html" style="width:100%; height: 75%; clear: both;"></iframe>
    </div>
</div>

<!-- <div style="width: 100%; height: 30%">
    <div style="width: 50%; height: 100%; float: left;">
        <iframe id="tool" src="http://<?=$_SERVER['HTTP_HOST']?>/tool.html" style="width:100%; height: 100%;"></iframe>
    </div>
    <div style="width: 50%; height: 100%; float: left;">
        <iframe id="upimg" src="http://ceshi.duowan.com/upimg/index.html" style="width:100%; height: 100%;"></iframe>
    </div>
</div> -->

</html>
