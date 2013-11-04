<?php include(APPPATH."views/header.php")?>

<div class="page secondary with-sidebar" id="page-index">

    <?php include(APPPATH."views/leftside.php")?>

    <div class="page-region">
    	<div class="page-region-content">
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