{assign var=baseUrl value='/linnaeus_ng/'}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
	<title>{$texts.page_title}</title>
	<link href="admin/media/system/favicon.ico" rel="shortcut icon" type="image/x-icon" />
	<link href="admin/media/system/favicon.ico" rel="icon" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="{$baseUrl}app/style/css/homepage.css">
	<link rel="stylesheet" type="text/css" href="app/style/css/ionicons.min.css">
	<link rel="stylesheet" type="text/css" href="app/style/css/homepage.css">

	<script type="text/javascript" src="{$baseUrl}app/vendor/bundle.js"></script>
	<script type="text/javascript" src="admin/javascript/main.js"></script>
	<script type="text/javascript" src="{$baseUrl}app/javascript/homepage.js"></script>
	<script type="text/javascript" src="app/javascript/homepage.js"></script>
</head>
<body>
	<div class="headerBar">
		<div class="headerContainer">
			<div class="headerText">
				{$texts.page_title}
			</div>
		</div>
	</div>
	<div class="container">
		<div class="header">
			<div class="logoContainer">
				<a href="#">
					<img width="128" height="190" alt="" src="{$baseUrl}app/media/system/skins/linnaeus_ng/naturalis-logo.svg" onerror="this.onerror=null; this.src='{$baseUrl}app/media/system/skins/linnaeus_ng/naturalis-logo.png'">
				</a>
			</div>
			<div class="headerImage">
				<img src="app/style/img/placeholderheader.png" alt="">
				<h1>{$texts.page_header}</h1>
			</div>
		</div>
		<div class="contentContainer">
			<div class="sidebar">
				<div class="search">
					<form action="" class="filterForm">
						<input type="text" name="" value="" placeholder="{$texts.search_placeholder}">
						<button><i class="ion-search"></i></button>
					</form>
				</div>
				<div class="sidebarContent">
					<h3>
						{$texts.left_bar_title}
					</h3>
					<p>{$texts.left_bar_text}</p>
				</div>
			</div>
			<div class="content">
				<ul class="identifiers">
					{foreach from=$projects key=k item=v}
					<li><span class="a" onclick="$('#p').val('{$v.id}');$('#theForm').submit();">{$v.title}</span></li>
					{/foreach}
				</ul>
			</div>

		</div>
		
	</div>
	<div class="footerContainer">
		<div class="footer">
			<!-- span class="copyright">© Naturalis en partners</span -->
			<a href=" https://science.naturalis.nl/en/ict/products/linnaeus-ng/" target="_blank" class="adminLink">
				<span class="powered">Powered by Naturalis Biodiversity Center & Linnaeus NG</span>
			</a>
		</div>
	</div>
<form method="post" id="theForm" action="app/views/linnaeus/set_project.php">
<input type="hidden" name="p" id="p" value="" />
<input type="hidden" name="rnd" value="{1|rand:99999999}" />
</form>

</body>
</html>
