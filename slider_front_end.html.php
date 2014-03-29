<?php






function front_end_slider($images, $paramssld, $slider)
{

	$slidertitle=$slider[0]->name;
	$sliderheight=$slider[0]->sl_height;
	$sliderwidth=$slider[0]->sl_width;
	$slidereffect=$slider[0]->slider_list_effects_s;
	$slidepausetime=$slider[0]->description;
	$sliderpauseonhover=$slider[0]->pause_on_hover;
	$slidechangespeed=$slider[0]->param;
?>

	<!--SLIDESHOW START-->
	<script>
	var sliderdata = [];      

	<?php
		$images=array_reverse($images);
		for($i=0;$i<count($images);$i++){
			echo 'sliderdata["'.$i.'"]=[];';
			
			echo 'sliderdata["'.$i.'"]["id"]="'.$i.'";';
			echo 'sliderdata["'.$i.'"]["image_url"]="'.$images[$i]->image_url.'";';
			
			
			$strdesription=str_replace('"',"'",$images[$i]->description);
			echo 'sliderdata["'.$i.'"]["description"]="'.$strdesription.'";';
			$current_image_description = $textarea_array[$i];
			
			$stralt=str_replace('"',"'",$images[$i]->name);
			echo 'sliderdata["'.$i.'"]["alt"]="'.$stralt.'";';
			$current_image_alt=$title_array[$i];
		}
	?>
       
    </script>
	
	<script type="text/javascript" src="<?php echo plugins_url('js/javascript.js.php', __FILE__) ?>?&pausetime=<?php echo $slidepausetime; ?>&speed=<?php echo $slidechangespeed; ?>&pausehover=<?php echo $sliderpauseonhover;  ?>&effect=<?php echo $slidereffect; ?>&height=<?php echo $sliderheight; ?>&width=<?php  echo $sliderwidth; ?>&cropresize=<?php echo $paramssld[slider_crop_image];?>"></script>

	<style>
		<?php		
			$slideshow_title_position = explode('-', trim($paramssld['slider_title_position']));
			$slideshow_description_position = explode('-', trim($paramssld['slider_description_position']));
		 ?>
		.huge_it_slideshow_image_wrap {
			height:<?php echo $sliderheight; ?>px;
			/*width:<?php  echo $sliderwidth; ?>px;*/
		}

		.huge_it_slideshow_title_span {
			text-align: <?php echo $slideshow_title_position[0]; ?>;
			vertical-align: <?php echo $slideshow_title_position[1]; ?>;
		}
		.huge_it_slideshow_description_span {
			text-align: <?php echo $slideshow_description_position[0]; ?>;
			vertical-align: <?php echo $slideshow_description_position[1]; ?>;
		}

		.huge_it_slideshow_image_wrap {
			background:#<?php echo $paramssld['slider_slider_background_color']; ?>;
			border-width:<?php echo $paramssld['slider_slideshow_border_size']; ?>px;
			border-color:#<?php echo $paramssld['slider_slideshow_border_color']; ?>;
			border-radius:<?php echo $paramssld['slider_slideshow_border_radius']; ?>px;
		}
		
		.huge_it_slideshow_title_text {
			color:#<?php echo $paramssld['slider_title_color']; ?>;
			
			background:<?php 
				if(isset($paramssld['slider_title_transparent']) and $paramssld['slider_title_transparent']=="checked"){
					
					list($r,$g,$b) = array_map('hexdec',str_split($paramssld['slider_title_background_color'],2));	
					echo 'rgba('.$r.','.$g.','.$b.',0.5)'; 
					
				}else{
					echo "none";
				}?>;
				
			font-size:<?php echo $paramssld['slider_title_font_size']; ?>px;
			border-width:<?php echo $paramssld['slider_title_border_size']; ?>px;
			border-color:#<?php echo $paramssld['slider_title_border_color']; ?>;
			border-radius:<?php echo $paramssld['slider_title_border_radius']; ?>px;
		}
		
		.huge_it_slideshow_description_text {
			color:#<?php echo $paramssld['slider_description_color']; ?>;
			background:<?php 
				if(isset($paramssld['slider_description_transparent']) and $paramssld['slider_description_transparent']=="checked"){
					list($r,$g,$b) = array_map('hexdec',str_split($paramssld['slider_description_background_color'],2));	
					echo 'rgba('.$r.','.$g.','.$b.',0.5)';
					
				}else{
					echo "none";
				}?>;
			font-size:<?php echo $paramssld['slider_description_font_size']; ?>px;
			border-width:<?php echo $paramssld['slider_description_border_size']; ?>px;
			border-color:#<?php echo $paramssld['slider_description_border_color']; ?>;
			border-radius:<?php echo $paramssld['slider_description_border_radius']; ?>px;
		}
		
		.huge_it_slideshow_dots_thumbnails {
			<?php if($paramssld['slider_dots_position']=="bottom"){?>
			bottom:0px;
			<?php }else if($paramssld['slider_dots_position']=="none"){?>
			display:none;
			<?
			}else{ ?>
			top:0px; <?php } ?>
		}
		
		.huge_it_slideshow_dots {
			background:#<?php echo $paramssld['slider_dots_color']; ?>;
		}
		
		.huge_it_slideshow_dots_active {
			background:#<?php echo $paramssld['slider_active_dot_color']; ?>;
		}
		
		<?php
		
		$arrowfolder=plugins_url('Front_images/arrows', __FILE__);
		switch ($paramssld['slider_navigation_type']) {
			case 1:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-21px;
						height:43px;
						width:29px;
						background:url(<?php echo $arrowfolder;?>/arrows.simple.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-21px;
						height:43px;
						width:29px;
						background:url(<?php echo $arrowfolder;?>/arrows.simple.png) right top no-repeat; 
					}
				<?php
				break;
			case 2:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.shadow.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.shadow.png) right top no-repeat; 
					}

					#huge_it_slideshow_left:hover {
						background-position:left -50px;
					}

					#huge_it_slideshow_right:hover {
						background-position:right -50px;
					}
				<?php
				break;
			case 3:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-22px;
						height:44px;
						width:44px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.simple.dark.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-22px;
						height:44px;
						width:44px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.simple.dark.png) right top no-repeat; 
					}

					#huge_it_slideshow_left:hover {
						background-position:left -44px;
					}

					#huge_it_slideshow_right:hover {
						background-position:right -44px;
					}
				<?php
				break;
			case 4:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-33px;
						height:66px;
						width:59px;
						background:url(<?php echo $arrowfolder;?>/arrows.cube.dark.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-33px;
						height:66px;
						width:59px;
						background:url(<?php echo $arrowfolder;?>/arrows.cube.dark.png) right top no-repeat; 
					}

					#huge_it_slideshow_left:hover {
						background-position:left -66px;
					}

					#huge_it_slideshow_right:hover {
						background-position:right -66px;
					}
				<?php
				break;
			case 5:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-18px;
						height:37px;
						width:40px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.blue.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-18px;
						height:37px;
						width:40px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.blue.png) right top no-repeat; 
					}

				<?php
				break;
			case 6:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.cube.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.cube.png) right top no-repeat; 
					}

					#huge_it_slideshow_left:hover {
						background-position:left -50px;
					}

					#huge_it_slideshow_right:hover {
						background-position:right -50px;
					}
				<?php
				break;
			case 7:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						right:0px;
						margin-top:-19px;
						height:38px;
						width:38px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.transparent.circle.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-19px;
						height:38px;
						width:38px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.transparent.circle.png) right top no-repeat; 
					}
				<?php
				break;
			case 8:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.png) right top no-repeat; 
					}
				<?php
				break;
			case 9:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.blue.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.blue.png) right top no-repeat; 
					}
				<?php
				break;
			case 10:
				?>
					#huge_it_slideshow_left {	
						left:0px;
						margin-top:-24px;
						height:48px;
						width:48px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.green.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right {
						right:0px;
						margin-top:-24px;
						height:48px;
						width:48px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.green.png) right top no-repeat; 
					}

					#huge_it_slideshow_left:hover {
						background-position:left -48px;
					}

					#huge_it_slideshow_right:hover {
						background-position:right -48px;
					}
				<?php
				break;
		}
?>
		
		
		
	</style>
	
<?php $slider_content='<div class="huge_it_slideshow_image_wrap">'; ?>
      <?php 
		  $current_image_id=0;
		  $current_pos =0;
		  $current_key=0;
		?>
		
  <?php $slider_content.='<!--################# DOTS ################# --><div class="huge_it_slideshow_dots_container">
          <div class="huge_it_slideshow_dots_thumbnails">'; ?>
            <?php
			$i=0;
			foreach($images as $key => $image_row){
              if ($key == $current_image_id) {
                $current_pos = $key * ($slideshow_filmstrip_width + 2);
                $current_key = $key;
              }
            ?>
			<?php
			$spanid1="huge_it_dots_".$key;
			$spanclass1="huge_it_slideshow_dots ".(($key==$current_image_id) ? 'huge_it_slideshow_dots_active' : 'huge_it_slideshow_dots_deactive');
			$spanonclick11="huge_it_change_image('-2', '";
			$spanonclick12="', sliderdata)";
			$spanonclick13='"
						  image_id="';
			$spanonclick14='"
						  image_key="';
			$spanonclick15=$spanonclick11.$key.$spanonclick12.$spanonclick13.$key.$spanonclick14.$key;
			$slider_content.='<span id="'.$spanid1.'" class="'.$spanclass1.'" onclick="'.$spanonclick15.'"></span>';
			?>
            <?php
            }
            ?>
 <?php  $slider_content.='</div>
        </div>';	?>
		<?php	
		$ahrefhref11="javascript:huge_it_change_image(parseInt(jQuery('#huge_it_current_image_key').val()), (parseInt(jQuery('#huge_it_current_image_key').val()) - iterator()) >= 0 ? (parseInt(jQuery('#huge_it_current_image_key').val()) - iterator()) % sliderdata.length : sliderdata.length - 1, sliderdata);";
		$slider_content.='<a id="huge_it_slideshow_left" href="'.$ahrefhref11.'"><span id="huge_it_slideshow_left-ico"><span><i class="huge_it_slideshow_prev_btn fa "></i></span></span></a>';
		$ahrefonclickjs11="javascript:huge_it_change_image(parseInt(jQuery('#huge_it_current_image_key').val()), (parseInt(jQuery('#huge_it_current_image_key').val()) + iterator()) % sliderdata.length, sliderdata);";
		$slider_content.=' <a id="huge_it_slideshow_right" href="'.$ahrefonclickjs11.'"><span id="huge_it_slideshow_right-ico"><span><i class="huge_it_slideshow_next_btn fa "></i></span></span></a>';
		?>
		

		 
		<!--################################## -->

	  
	  <!--################ IMAGES ################## -->
	  <?php 
	  $slider_content.='
      <div id="huge_it_slideshow_image_container"  width="100%" class="huge_it_slideshow_image_container">        
        <div class="huge_it_slide_container" width="100%">
          <div class="huge_it_slide_bg">
            <div class="huge_it_slider">';

		 
			$i=0;
			foreach($images as $key => $image_row){
         
            if ($i == $current_image_id) {
            $current_key = $key;
			  $slider_content.='<span class="huge_it_slideshow_image_span" id="image_id_'.$i.'">
				<span class="huge_it_slideshow_image_span1">
                  <span class="huge_it_slideshow_image_span2">';
					if($image_row->sl_url!=""){ $slider_content.='<a href="'.$image_row->sl_url.'">';}
						$slider_content.='<img id="huge_it_slideshow_image"
							 class="huge_it_slideshow_image"
							 src="'.$image_row->image_url.'"
							 image_id="'.$i.'" />';
					if(!isset($image_row->sl_url) or $image_row->sl_url!=""){$slider_content.='</a>';}
                 $slider_content.=' </span>
                </span>
              </span>';
				$slider_content.='<input type="hidden" id="huge_it_current_image_key" value="'.$key.'" />'; 

            }
            else {
			$slider_content.='
              <span class="huge_it_slideshow_image_second_span" id="image_id_'.$i.'">
                <span class="huge_it_slideshow_image_span1">
                  <span class="huge_it_slideshow_image_span2">';
					if($image_row->sl_url!=""){ $slider_content.='<a href="'.$image_row->sl_url.'">';}
					$slider_content.='<img id="huge_it_slideshow_image_second" class="huge_it_slideshow_image" src="'.$image_row->image_url.'" />';
					if(!isset($image_row->sl_url) or $image_row->sl_url!=""){$slider_content.='</a>';}
                  $slider_content.='</span>
                </span>
              </span>';

            }
			$i++;
          }
			$slider_content.='</div>
          </div>
        </div>

      </div>'; ?>
	
	
	
	<!--################ TITLE ################## -->
	<?php $slider_content.='
      <div class="huge_it_slideshow_image_container" style="position: absolute;">
        <div class="huge_it_slideshow_title_container">
          <div style="display:table; margin:0 auto;">
            <span class="huge_it_slideshow_title_span">';
			 ?>
			 <?php if($images[0]->name=='')$titleecho= 'none'; ?>
			<?php $slider_content.='<div class="huge_it_slideshow_title_text '.$titleecho.'" >
					'.$images[0]->name.' 
			   </div>
            </span>
          </div>
        </div>
      </div>';
	  ?>
	 
      <?php 
   

      ?>
	  <!--################ DESCRIPTION ################## -->
	  <?php $slider_content.='
      <div class="huge_it_slideshow_image_container" style="position: absolute;">
        <div class="huge_it_slideshow_title_container">
          <div style="display:table; margin:0 auto;">
            <span class="huge_it_slideshow_description_span">';
			?>
			<?php if($images[0]->description=='')$titleecho= 'none'; ?>
            <?php $slider_content.='<div class="huge_it_slideshow_description_text '.$titleecho.'">
                       '.$images[0]->description.'
			  </div>
            </span>
          </div>
        </div>
      </div>
    </div>';
	?>

<!--SLIDESHOW END-->

<?php
    return $slider_content;
}
?>