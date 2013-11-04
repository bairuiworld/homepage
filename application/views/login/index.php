<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html" style="height: 100%">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="target-densitydpi=device-dpi, width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="My HomePage">
    <meta name="author" content="Ge Rui">
    <meta name="keywords" content="windows 8, modern style, Metro UI, style, modern, css, framework">

    <link href="<?php echo base_url();?>metro/css/modern.css" rel="stylesheet">
    <link href="<?php echo base_url();?>metro/css/modern-responsive.css" rel="stylesheet">
    <link href="<?php echo base_url();?>metro/css/site.css" rel="stylesheet" type="text/css">
    <link href="<?php echo base_url();?>metro/js/google-code-prettify/prettify.css" rel="stylesheet" type="text/css">

    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/moment.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/assets/moment_langs.js"></script>

    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/dropdown.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/accordion.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/buttonset.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/carousel.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/input-control.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/pagecontrol.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/rating.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/slider.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/tile-slider.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/tile-drag.js"></script>
    <script type="text/javascript" src="<?php echo base_url();?>metro/js/modern/calendar.js"></script>

    <title>GeRui's Home</title>
</head>
<body style="height: 100%" class="metrouicss bg-color-purple">
	<div class="grid">
    	<div class="row">
    		<div class="span6">
    		</div>
			<div class="span4 " style="margin: 20px">
				<h1 style="text-align:center" class="fg-color-orange">登录</h1>
				<form style="margin: 20px 0px;" action="<?php echo site_url('login/chk_login')?>">


					<div class="input-control text">
						<input type="text"/>
						<button class="helper"></button>
					</div>
					<div class="input-control password">
						<input type="password">
						<button class="helper"></button>
                    </div>
                    <div class="place-right">
						<input type="submit"  value="登录" style="margin: 0px"/>
					</div>
				</form>
			</div>
		</div>
	</div>



</body>

</html>
