<?php include(APPPATH."views/header.php")?>

<div class="page secondary with-sidebar" id="page-index">

    <div class="page-header">
    	<div class="page-header-content">
    		<h1>相关<small>信息</small></h1>
            <a href="<?php echo base_url();?>" class="back-button big page-back"></a>
        </div>
    </div>

    <?php include(APPPATH."views/leftside.php")?>

    <div class="page-region">
    	<div class="page-region-content">
            <div class="grid">
	            <div class="row">
	            	<ul class="accordion dark" data-role="accordion">
	                    <li class="">
	                        <a href="#">个人信息</a>
	                        <div style="overflow: hidden; display: none;">
	                            <div class="tile double bg-color-blueDark">
				                    <div class="tile-content">
				                        <img src="<?php echo base_url();?>metro/images/profile.png" class="place-right">
				                        	<h4 style="margin-bottom: 5px;">Ge Rui</h4>
			                        		<p>&nbsp;</p>
			                        		<p>邮箱:&nbsp;forgerui@gmail.com</p>
			                        		<p>个人博客:&nbsp;www.forgerui.tk/blog</p>
			                        		<p></p>
			                        </div>
			                        <div class="brand">
			                        	<span class="name">新浪微博: @brorld</span>
			                        </div>
			                   </div>
	                        </div>
	                    </li>

	                    <li class="">
	                        <a href="#">网站信息</a>
	                        <div style="overflow: hidden; display: none;">
	                           <div class="grid span9">
						            <div class="row">
							            <div class="span9" style="background-color: #ccc; height: 200px;">
							                 <div class="page span3 snapped bg-color-blue">
							                 	<p style="padding: 20px; ">网站信息</p>
							                 </div>
							                 <div class="page span8 fill bg-color-purple">
							                 		<p style="padding: 20px;font-size:20px;font-style:bold;">Ge Rui的个人主页</p>
							                 		<p style="padding: 5px;"><span>特色：使用Metro UI CSS 框架，Win8风格</span></p>
							                 		<p style="padding: 5px;"><span>版本: v1.0</span></p>
							                 </div>
							            </div>
							        </div>
						        </div>
	                        </div>
	                    </li>
                	</ul>
		        </div>
	        </div>

        </div>
    </div>

</div>




<?php include(APPPATH."views/footer.php")?>