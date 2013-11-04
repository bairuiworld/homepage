<?php include(APPPATH."views/header.php")?>


<div class="page secondary with-sidebar" id="page-index">
    <?php include(APPPATH."views/leftside.php")?>

    <div class="page-region">
    	<div class="page-region-content">
			<div class="grid">
				<div class="row">
					<div class="span2 offset7">
						<?php $login_url = site_url('login');?>
						<button class="image-button place-right bg-color-blueDark fg-color-white" style="margin: 0px" onclick="document.location.href='<?php echo $login_url;?>'">
							登录
							<i class="icon-user" style="margin: 0px 10px 0px 0px"></i>
						</button>
					</div>
				</div>
			</div>
    		<div class="grid">
	            <div class="row">
					<div class="span9">
						<div class="carousel" style="height: 200px;" data-role="carousel" data-param-period="3000" data-param-effect="fade" >
							<div class="slides">
								<div class="slide image" id="slide1" >
                                	<img src="<?php echo base_url();?>metro/images/panner1.jpg">
                                    <div class="description">
                                    	1
                                    </div>
                                </div>
                                <div class="slide image" id="slide2" >
                                    <img src="<?php echo base_url();?>metro/images/panner2.jpg">
                                    <div class="description">
                                    	2
                                    </div>
                                </div>
                                <div class="slide image" id="slide3">
                                    <img src="<?php echo base_url();?>metro/images/panner3.jpg">
                                    <div class="description">
                                    	3
                                    </div>
                                </div>
                                <div class="slide image" id="slide4">
                                    <img src="<?php echo base_url();?>metro/images/panner4.jpg">
                                    <div class="description">
                                    	4
                                    </div>
                                </div>
                            </div>
							<!-- <span class="control left bg-color-darken">‹</span>
							<span class="control right bg-color-darken">›</span> -->
                        </div>
					</div>
		        </div>
	        </div>

        </div>
    </div>

</div>




<?php include(APPPATH."views/footer.php")?>