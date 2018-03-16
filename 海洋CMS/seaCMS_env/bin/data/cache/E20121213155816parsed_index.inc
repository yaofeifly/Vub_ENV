<!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta name="renderer" content="webkit">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="/templets/default/images/bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="/templets/default/images/system.css">
<script src="/templets/default/images/jquery/1.11.3/jquery.min.js"></script>
<script src="/templets/default/images/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<!--[if lt IE 9]>
<script src="/templets/default/images/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="/templets/default/images/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<script>var sitePath=''</script>
<script src="/templets/default/images/system.js"></script>
<script src="/js/common.js"></script>
<script src="/js/function.js"></script>
<title>海洋CMS</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
<div class="sea-header hidden-xs">
<div class="container">
	<div class="row">
  	<div class="col-xs-3">
    	<a href="http://218.2.197.238" class="sea-logo"></a>
    </div>
  	<div class="col-xs-7">
    	<form class="form-horizontal sea-search" id="sea-search" role="search" action="" method="post">
        <div class="form-group">
        	<div class="col-sm-9">
          <input type="text" class="form-control" id="searchword" name="searchword" placeholder="Search">
          </div>
          <div class="col-sm-3 sea-col-0">    
				<button type="submit" class="btn btn-default" data-module="vod" data-action="/search.php">搜视频</button>		  
        		<button type="submit" class="btn btn-default btn-success" data-module="news" data-action="/so.php">搜资讯</button>
				
          </div>
        </div>
      </form>
    </div><!-- /.col-xs-5 -->
  </div><!-- /.row -->
</div>
</div>
<nav class="navbar navbar-inverse visible-lg visible-md" role="navigation">
  <div class="container">
    <ul class="nav navbar-nav navbar-left">
<li class=""><a href="http://218.2.197.238" class="dropdown-toggle" data-toggle="">首页</a></li>

        <li class="dropdown">
          <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">新闻<b class="caret"></b></a>
          <ul class="dropdown-menu">
            
			<li><a href="/list/?5.html">体育</a></li>
			
			<li><a href="/list/?6.html">汽车</a></li>
			
			<li><a href="/list/?7.html">科技</a></li>
			
			<li><a href="/list/?8.html">财经</a></li>
			
			<li><a href="/list/?9.html">股市</a></li>
			
			<li><a href="/list/?10.html">地方</a></li>
			
			<li><a href="/list/?11.html">母婴</a></li>
			
			<li><a href="/list/?12.html">生活</a></li>
			
          </ul>
        </li>

        <li class="dropdown">
          <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">娱乐<b class="caret"></b></a>
          <ul class="dropdown-menu">
            
			<li><a href="/list/?13.html">明星</a></li>
			
			<li><a href="/list/?14.html">音乐</a></li>
			
			<li><a href="/list/?15.html">游戏</a></li>
			
			<li><a href="/list/?16.html">原创</a></li>
			
          </ul>
        </li>
	


        <li class="">
          <a href="/list/?3.html" class="dropdown-toggle" data-toggle="">军事</a>
        </li>

        <li class="">
          <a href="/list/?4.html" class="dropdown-toggle" data-toggle="">猎奇</a>
        </li>
	
<li class=""><a href="/topic/?1.html" class="dropdown-toggle" data-toggle="">专辑</a></li>
<li class=""><a href="/news/" class="dropdown-toggle" data-toggle="">文章</a></li>		
<li class=""><a href="/gbook.php"  class="dropdown-toggle" data-toggle="">留言求片</a></li>
<li class=""><a href="/member.php" class="dropdown-toggle" data-toggle="">会员中心</a></li>
<li class=""><a href="/new.html" class="dropdown-toggle" data-toggle="">最近更新</a></li>
<li class=""><a href="/top/hot.html" class="dropdown-toggle" data-toggle="">热门排行</a></li>

    </ul>
  </div><!-- /.container -->
</nav><!-- /.navbar -->
<div class="container sea-nav visible-xs visible-sm">
	<div class="row">
    <ul class="list-inline">
      <li class="col-xs-3 text-left">
        <h4><a href="javascript:;" id="sea-goback"><span class="glyphicon glyphicon-chevron-left"></span></a></h4>
      </li>
      <li class="col-xs-6 text-center">
        <h4>海洋CMS</h4>
      </li>
      <li class="col-xs-3 text-right"><h4>
        <a href="http://218.2.197.238"><span class="glyphicon glyphicon-home"></span></a>
        <a href="/search.php?searchword=请输入关键词"><span class="glyphicon glyphicon-search"></span></a></h4>
      </li>
    </ul>
  </div>
</div>
<switch name="list_id" >
<case value="1"><script>seacms.cms.nav('dianying');</script></case>
<case value="2"><script>seacms.cms.nav('tv');</script></case>
<case value="3"><script>seacms.cms.nav('dongman');</script></case>
<case value="4"><script>seacms.cms.nav('zongyi');</script></case>
<case value="5"><script>seacms.cms.nav('gaoxiao');</script></case>
<default /><script>seacms.cms.nav('{$model}');</script>
</switch>
<div class="container sea-bg">
  <div class="row">
    <div class="col-md-8 sea-slide-pic">
	  <div class="carousel slide" id="sea-slide">
  <!-- 轮播（Carousel）指标 -->
  <ol class="carousel-indicators">

    </volist>
  </ol>   
  <!-- 轮播（Carousel）项目 -->
  <div class="carousel-inner">

  
	  
	  
  </div>
  <!-- 轮播（Carousel）导航 -->
  <a class="carousel-control left" href="#sea-slide" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>
  <a class="carousel-control right" href="#sea-slide" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a>
</div>
<script>$('#sea-slide').carousel({interval: 2000 });</script>
    </div>
    <ol class="col-md-4 sea-slide-new visible-lg visible-md">
      
    </ol>
    <div class="clearfix"></div>
    <ul class="row list-unstyled text-center index-nav visible-xs visible-sm">
      
      <li class="col-xs-4">
        <a href="/list/?1.html" class="btn btn-success btn-block">新闻</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?2.html" class="btn btn-success btn-block">娱乐</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?3.html" class="btn btn-success btn-block">军事</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?4.html" class="btn btn-success btn-block">猎奇</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?5.html" class="btn btn-success btn-block">体育</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?6.html" class="btn btn-success btn-block">汽车</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?7.html" class="btn btn-success btn-block">科技</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?8.html" class="btn btn-success btn-block">财经</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?9.html" class="btn btn-success btn-block">股市</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?10.html" class="btn btn-success btn-block">地方</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?11.html" class="btn btn-success btn-block">母婴</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?12.html" class="btn btn-success btn-block">生活</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?13.html" class="btn btn-success btn-block">明星</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?14.html" class="btn btn-success btn-block">音乐</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?15.html" class="btn btn-success btn-block">游戏</a>
      </li>
      
      <li class="col-xs-4">
        <a href="/list/?16.html" class="btn btn-success btn-block">原创</a>
      </li>
       
	  <li class="col-xs-4"><a href="/topic/?1.html" class="btn btn-success btn-block">专辑</a></li>
	  <li class="col-xs-4"><a href="/news/" class="btn btn-success btn-block">文章</a></li>		
      <li class="col-xs-4"><a href="/gbook.php" class="btn btn-success btn-block">留言</a></li>
	  <li class="col-xs-4"><a href="/member.php" class="btn btn-success btn-block">会员中心</a></li>
	  <li class="col-xs-4"><a href="/new.html" class="btn btn-success btn-block">最近更新</a></li>
	  <li class="col-xs-4"><a href="/top/hot.html" class="btn btn-success btn-block">热门排行</a></li>
    </ul>
  </div>
</div>
<div class="clearfix sea-clearfix"></div> 


<!--大分类框架1--->
<div class="container sea-bg">
  <div class="row">
    <div class="col-md-8 sea-col index-left">
    <div class="page-header">
      <h4>
        <span class="glyphicon glyphicon-film sea-text"></span> 
		
		<a href="/list/?1.html">新闻</a>
		
        <small class="hidden-xs">
		
		<a href="/list/?5.html">体育</a>
		
		<a href="/list/?6.html">汽车</a>
		
		<a href="/list/?7.html">科技</a>
		
		<a href="/list/?8.html">财经</a>
		
		<a href="/list/?9.html">股市</a>
		
		<a href="/list/?10.html">地方</a>
		
		<a href="/list/?11.html">母婴</a>
		
		<a href="/list/?12.html">生活</a>
		
		</small>
      </h4>
    </div>
    <!-- -->
    <ul class="list-unstyled">
   
    </ul>
    </div><!--lg-8end -->
    <div class="col-md-4 sea-col index-right visible-lg visible-md">
      <div class="page-header">
        <h4><span class="glyphicon glyphicon-signal sea-text"></span> 热播榜</h4>
      </div>
      <ol class="sea-vod-hot">
         
      </ol>
      <ul class="list-unstyled sea-vod-type">
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=农村">农村</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=恐怖">恐怖</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=宫廷">宫廷</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=剧情">剧情</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=言情">言情</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=家庭">家庭</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=励志">励志</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=偶像">偶像</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=都市">都市</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=时装">时装</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=年代">年代</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=生活">生活</a></li>
      </ul>
    </div><!--lg-4 end -->
  </div><!--row end -->
</div>
<div class="clearfix sea-clearfix"></div>

<!--大分类框架2--->
<div class="container sea-bg">
  <div class="row">
    <div class="col-md-8 sea-col index-left">
    <div class="page-header">
      <h4>
        <span class="glyphicon glyphicon-film sea-text"></span> 
		
		<a href="/list/?2.html">娱乐</a>
		
        <small class="hidden-xs">
		
		<a href="/list/?13.html">明星</a>
		
		<a href="/list/?14.html">音乐</a>
		
		<a href="/list/?15.html">游戏</a>
		
		<a href="/list/?16.html">原创</a>
		
		</small>
      </h4>
    </div>
    <!-- -->
    <ul class="list-unstyled">
   
    </ul>
    </div><!--lg-8end -->
    <div class="col-md-4 sea-col index-right visible-lg visible-md">
      <div class="page-header">
        <h4><span class="glyphicon glyphicon-signal sea-text"></span> 热播榜</h4>
      </div>
      <ol class="sea-vod-hot">
         
      </ol>
      <ul class="list-unstyled sea-vod-type">
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2015">2015</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&year=2014">2014</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2013">2013</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2012">2012</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&year=2011">2011</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2010">2010</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2009">2009</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&year=2008">2008</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2007">2007</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&year=2006">2006</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2005">2005</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&year=2004">2004</a></li>
      </ul>
    </div><!--lg-4 end -->
  </div><!--row end -->
</div>
<div class="clearfix sea-clearfix"></div>

<!--大分类框架3--->
<div class="container sea-bg">
  <div class="row">
    <div class="col-md-8 sea-col index-left">
    <div class="page-header">
      <h4>
        <span class="glyphicon glyphicon-film sea-text"></span> 
		
		<a href="/list/?3.html">军事</a>
		
        <small class="hidden-xs">
		
		</small>
      </h4>
    </div>
    <!-- -->
    <ul class="list-unstyled">
   
    </ul>
    </div><!--lg-8end -->
    <div class="col-md-4 sea-col index-right visible-lg visible-md">
      <div class="page-header">
        <h4><span class="glyphicon glyphicon-signal sea-text"></span> 热播榜</h4>
      </div>
      <ol class="sea-vod-hot">
         
      </ol>
      <ul class="list-unstyled sea-vod-type">
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&area=大陆">大陆</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=香港">香港</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=台湾">台湾</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&area=韩国">韩国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=泰国">泰国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&area=美国">美国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=日本">日本</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&area=法国">法国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=印度">印度</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=英国">英国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&area=德国">德国</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&area=其它">其它</a></li>
      </ul>
    </div><!--lg-4 end -->
  </div><!--row end -->
</div>
<div class="clearfix sea-clearfix"></div>

<!--大分类框架4--->
<div class="container sea-bg">
  <div class="row">
    <div class="col-md-8 sea-col index-left">
    <div class="page-header">
      <h4>
        <span class="glyphicon glyphicon-film sea-text"></span> 
		
		<a href="/list/?4.html">猎奇</a>
		
        <small class="hidden-xs">
		
		</small>
      </h4>
    </div>
    <!-- -->
    <ul class="list-unstyled">
   
    </ul>
    </div><!--lg-8end -->
    <div class="col-md-4 sea-col index-right visible-lg visible-md">
      <div class="page-header">
        <h4><span class="glyphicon glyphicon-signal sea-text"></span> 热播榜</h4>
      </div>
      <ol class="sea-vod-hot">
         
      </ol>
      <ul class="list-unstyled sea-vod-type">
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=农村">农村</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=恐怖">恐怖</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=宫廷">宫廷</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=剧情">剧情</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=言情">言情</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=家庭">家庭</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=励志">励志</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=偶像">偶像</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=都市">都市</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-success" href="/search.php?searchtype=5&jq=时装">时装</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=年代">年代</a></li>
<li class="col-md-3 sea-text-hidden sea-col"><a class="btn btn-sm btn-default" href="/search.php?searchtype=5&jq=生活">生活</a></li>
      </ul>
    </div><!--lg-4 end -->
  </div><!--row end -->
</div>
<div class="clearfix sea-clearfix"></div>




</div>
</div>
<div class="container sea-bg hidden-xs">
<div class="row">
	<div class="col-xs-12 sea-col">
  	<div class="page-header">
      <h4><span class="glyphicon glyphicon-link sea-text"></span> 友情链接</h4>
    </div>
    <p class="sea-link">
    
	<a href="http://www.seacms.net" target="_blank">海洋cms</a>
	
    </p>
  </div>
</div>
</div>
<div class="clearfix sea-clearfix"></div>
<div class="container sea-bg sea-footer">
  <div class="row">
    <div class="col-md-10 col-sm-12 col-xs-12">
      <p>友情提示：请勿长时间观看影视，注意保护视力并预防近视，合理安排时间，享受健康生活。</p>
      <p>网站简介：努力打造海洋影视管理系统为最好的PHP影视系统!</p>
      <p>版权声明：本网站为非赢利性站点，本网站所有内容均来源于互联网相关站点自动搜索采集信息，相关链接已经注明来源。</p>
      <p>本站程序基于<a href="http://www.seacms.net" target="_blank"  style="color:#0000FF">海洋CMS</a>，<font color="#0000FF">{seacms:runinfo}</font></p>
    </div>
    <ul class="col-md-2 hidden-sm hidden-xs">
      <li>{seacms:member}</li>
      <li><a href="/gbook.php" >留言反馈</a></li>
	  <li><a href="/new.html" >最近更新</a></li>
	  <li><a href="/hot/hot.html" >热门排行</a></li>
      <li><a href="http://www.seacms.net" target="_blank">海洋cms</a></li>
    </ul>  
  </div>
</div>
<span style="display:none"></span>
</body>
</html>