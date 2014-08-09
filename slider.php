<?php

/*
Plugin Name: Huge IT slider
Plugin URI: http://huge-it.com/slider
Description: Huge IT slider is a convenient tool for organizing the images represented on your website into sliders. Each product on the slider is assigned with a relevant slider, which makes it easier for the customers to search and identify the needed images within the slider.
Version: 2.5.5
Author: http://huge-it.com/
License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
*/

add_action('media_buttons_context', 'add_my_custom_button');

function add_my_custom_button($context) {
  
  $img = plugins_url( '/images/post.button.png' , __FILE__ );
  $container_id = 'huge_it_slider';

  $title = 'Select Huge IT Slider to insert into post';

  $context .= '<a class="button thickbox" title="Select slider to insert into post"    href="?page=sliders_huge_it_slider&task=add_shortcode_post&TB_iframe=1&width=400&inlineId='.$container_id.'">
		<span class="wp-media-buttons-icon" style="background: url('.$img.'); background-repeat: no-repeat; background-position: left bottom;"></span>
	Add Slider
	</a>';
  
  return $context;
}


add_action('init', 'hugesl_do_output_buffer');
function hugesl_do_output_buffer() {
        ob_start();
}
add_action('init', 'slider_lang_load');

function slider_lang_load()
{
    load_plugin_textdomain('sp_slider', false, basename(dirname(__FILE__)) . '/Languages');

}
$ident = 1;
add_action('admin_head', 'huge_it_ajax_func');
function huge_it_ajax_func()
{
    ?>
    <script>
        var huge_it_ajax = '<?php echo admin_url("admin-ajax.php"); ?>';
    </script>
<?php
}
function huge_it_slider_images_list_shotrcode($atts)
{
    extract(shortcode_atts(array(
        'id' => 'no huge_it slider',
    
    ), $atts));
    return huge_it_cat_images_list($atts['id']);
}
function slider_after_search_results($query)
{
    global $wpdb;
    if (isset($_REQUEST['s']) && $_REQUEST['s']) {
        $serch_word = htmlspecialchars(($_REQUEST['s']));
        $query = str_replace($wpdb->prefix . "posts.post_content", gen_string_slider_search($serch_word, $wpdb->prefix . 'posts.post_content') . " " . $wpdb->prefix . "posts.post_content", $query);
    }
    return $query;
}
add_filter('posts_request', 'slider_after_search_results');
function gen_string_slider_search($serch_word, $wordpress_query_post)
{
    $string_search = '';
    global $wpdb;
    if ($serch_word) {
        $rows_slider = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders WHERE (description LIKE %s) OR (name LIKE %s)", '%' . $serch_word . '%', "%" . $serch_word . "%"));
        $count_cat_rows = count($rows_slider);
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="1" %\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="1"%\' OR ';
        }
        $rows_slider = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_sliders WHERE (name LIKE %s)","'%" . $serch_word . "%'"));
        $count_cat_rows = count($rows_slider);
        for ($i = 0; $i < $count_cat_rows; $i++) {
            $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="0"%\' OR ' . $wordpress_query_post . ' LIKE \'%[huge_it_slider id="' . $rows_slider[$i]->id . '" details="0"%\' OR ';
        }
        $rows_single = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "huge_itslider_images WHERE name LIKE %s","'%" . $serch_word . "%'"));
        $count_sing_rows = count($rows_single);
        if ($count_sing_rows) {
            for ($i = 0; $i < $count_sing_rows; $i++) {
                $string_search .= $wordpress_query_post . ' LIKE \'%[huge_it_slider_Product id="' . $rows_single[$i]->id . '"]%\' OR ';
            }
        }
    }
    return $string_search;
}
add_shortcode('huge_it_slider', 'huge_it_slider_images_list_shotrcode');
function   huge_it_cat_images_list($id)
{
    require_once("slider_front_end.html.php");
    require_once("slider_front_end.php");
    if (isset($_GET['product_id'])) {
        if (isset($_GET['view'])) {
            if ($_GET['view'] == 'huge_itslider') {
                return showPublishedimages_1($id);
            } else {
                return front_end_single_product($_GET['product_id']);
            }
        } else {
            return front_end_single_product($_GET['product_id']);
        }
    } else {
        return showPublishedimages_1($id);
    }
}
add_filter('admin_head', 'huge_it_cat_ShowTinyMCE');
function huge_it_cat_ShowTinyMCE()
{
    wp_enqueue_script('common');
    wp_enqueue_script('jquery-color');
    wp_print_scripts('editor');
    if (function_exists('add_thickbox')) add_thickbox();
    wp_print_scripts('media-upload');
    if (version_compare(get_bloginfo('version'), 3.3) < 0) {
        if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    }
    wp_admin_css();
    wp_enqueue_script('utils');
    do_action("admin_print_styles-post-php");
    do_action('admin_print_styles');
}



add_action('admin_menu', 'huge_it_slider_options_panel');
function huge_it_slider_options_panel()
{
    $page_cat = add_menu_page('Theme page title', 'Huge IT slider', 'manage_options', 'sliders_huge_it_slider', 'sliders_huge_it_slider', plugins_url('images/huge_it_sliderLogoHover -for_menu.png', __FILE__));
    add_submenu_page('sliders_huge_it_slider', 'Sliders', 'Sliders', 'manage_options', 'sliders_huge_it_slider', 'sliders_huge_it_slider');
    $page_option = add_submenu_page('sliders_huge_it_slider', 'General Options', 'General Options', 'manage_options', 'Options_slider_styles', 'Options_slider_styles');
	add_submenu_page( 'sliders_huge_it_slider', 'Licensing', 'Licensing', 'manage_options', 'huge_it_slider_Licensing', 'huge_it_slider_Licensing');

   	add_action('admin_print_styles-' . $page_cat, 'huge_it_slider_admin_script');
    add_action('admin_print_styles-' . $page_option, 'huge_it_slider_option_admin_script');
}


function huge_it_slider_admin_script()
{
		wp_enqueue_media();
		wp_enqueue_style("jquery_ui", "http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css", FALSE);
		wp_enqueue_script("jquery_new", "http://code.jquery.com/jquery-1.10.2.js", FALSE);
		wp_enqueue_script("jquery_ui_new", "http://code.jquery.com/ui/1.10.4/jquery-ui.js", FALSE);
		
		wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
		wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
		wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
}

function huge_it_slider_option_admin_script()
{
		wp_enqueue_media();
		wp_enqueue_script("jquery_old", "http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js", FALSE);
		wp_enqueue_style("jquery_ui", "http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css", FALSE);

		
		wp_enqueue_script("simple_slider_js",  plugins_url("js/simple-slider.js", __FILE__), FALSE);
		wp_enqueue_style("simple_slider_css", plugins_url("style/simple-slider.css", __FILE__), FALSE);
		
		wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
		wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
		wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
}



function huge_it_slider_Licensing(){

	?>
    <div style="width:95%">
    <p>
	This plugin is the non-commercial version of the Huge IT slider. If you want to customize to the styles and colors of your website,than you need to buy a license.
Purchasing a license will add possibility to customize the general options of the Huge IT slider. 

 </p>
<br /><br />
<a href="http://huge-it.com/slider/" class="button-primary" target="_blank">Purchase a License</a>
<br /><br /><br />
<p>After the purchasing the commercial version follow this steps:</p>
<ol>
	<li>Deactivate Huge IT slider Plugin</li>
	<li>Delete Huge IT slider Plugin</li>
	<li>Install the downloaded commercial version of the plugin</li>
</ol>
</div>
<?php
	}
function huge_it_option_admin_script()
{
   wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
}
function sliders_huge_it_slider()
{
    require_once("sliders.php");
    require_once("sliders.html.php");
    if (!function_exists('print_html_nav'))
        require_once("slider_function/html_slider_func.php");
    if (isset($_GET["task"]))
        $task = $_GET["task"]; 
    else
        $task = '';
    if (isset($_GET["id"]))
        $id = $_GET["id"];
    else
        $id = 0;
    global $wpdb;
    switch ($task) {
        case 'add_cat':
            add_slider();
            break;
		case 'add_shortcode_post':
            add_shortcode_post();
            break;
		case 'popup_posts':
            if ($id)
                popup_posts($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itslider_sliders");
                popup_posts($id);
            }
            break;
        case 'edit_cat':
            if ($id)
                editslider($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itslider_sliders");
                editslider($id);
            }
            break;
        case 'save':
            if ($id)
                apply_cat($id);
        case 'apply':
            if ($id) {
                apply_cat($id);
                editslider($id);
            } 
            break;
        case 'remove_cat':
            removeslider($id);
            showslider();
            break;
        default:
            showslider();
            break;
    }
}

function add_shortcode_post()
{
	?>
<script type="text/javascript">
				jQuery(document).ready(function() {
				  jQuery('#hugeitsliderinsert').on('click', function() {
					jQuery('#save-buttom').click();
					var id = jQuery('#huge_it_slider-select option:selected').val();
					if(window.parent.tinyMCE && window.parent.tinyMCE.activeEditor)
					{
						window.parent.send_to_editor('[huge_it_slider id="'+id+'"]');
					}
					tb_remove();
				  })
				});
</script>
<style>
#wpadminbar {
	display: none;
}
#wpcontent {
	margin-top: -55px;
}

.wp-core-ui .button {margin:0px 0px 0px 10px !important;}

#slider-unique-options-list li {
	clear:both;
	margin:10px 0px 5px 0px;
}

#slider-unique-options-list li label {width:130px;}

#save-buttom {display:none;}
</style>
<h3>Select the slider</h3>
<div id="huge_it_slider">
  <?php 
  	  global $wpdb;
	  $query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders";
	  $firstrow=$wpdb->get_row($query);
	  if(isset($_POST["hugeit_slider_id"])){
	  $id=$_POST["hugeit_slider_id"];
	  }
	  else{
	  $id=$firstrow->id;
	  }
	  if($_GET["htslider_id"] == $_POST["hugeit_slider_id"]){
	  if($_GET["hugeit_save"]==1){
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_width = '".$_POST["sl_width"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_height = '".$_POST["sl_height"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  pause_on_hover = '".$_POST["pause_on_hover"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  slider_list_effects_s = '".$_POST["slider_effects_list"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  description = '".$_POST["sl_pausetime"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  param = '".$_POST["sl_changespeed"]."'  WHERE id = '".$id."' ");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_position = '".$_POST["sl_position"]."'  WHERE id = '".$id."' ");
		}
		}
	  $query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders order by id ASC";
			   $shortcodesliders=$wpdb->get_results($query);
		$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders WHERE id= %d", $id);
	   $row=$wpdb->get_row($query);
			   ?>
<form action="?page=sliders_huge_it_slider&task=add_shortcode_post&TB_iframe=1&width=400&inlineId=<?php echo $container_id; ?>&hugeit_save=1&htslider_id=<?php echo $id; ?>" method="post" name="adminForm" id="adminForm">
 <?php 	if (count($shortcodesliders)) {
							echo "<select id='huge_it_slider-select' onchange='this.form.submit()' name='hugeit_slider_id'>";
							foreach ($shortcodesliders as $shortcodeslider) {
								$selected='';
								if($shortcodeslider->id == $_POST["hugeit_slider_id"]){$selected='selected="selected"';} 
								echo "<option ".$selected." value='".$shortcodeslider->id."'>".$shortcodeslider->name."</option>";
							}
							echo "</select>";
							echo "<button class='button primary' id='hugeitsliderinsert'>Insert Slider</button>";
						} else {
							echo "No slideshows found", "huge_it_slider";
						}
						$container_id = 'huge_it_slider';
						?>
	
</div>
			
				<div id="" class="meta-box-sortables ui-sortable">
					<div id="slider-unique-options" class="">
					<h3 class="hndle"><span>Current Slider Options</span></h3>
					<ul id="slider-unique-options-list">
						<li>
							<label for="sl_width">Width</label>
							<input type="text" name="sl_width" id="sl_width" value="<?php echo $row->sl_width; ?>" class="text_area" />
						</li>
						<li>
							<label for="sl_height">Height</label>
							<input type="text" name="sl_height" id="sl_height" value="<?php echo $row->sl_height; ?>" class="text_area" />
						</li>
						<li>
							<label for="pause_on_hover">Pause on hover</label>
							<input type="hidden" value="off" name="pause_on_hover" />					
							<input type="checkbox" name="pause_on_hover"  value="on" id="pause_on_hover"  <?php if($row->pause_on_hover  == 'on'){ echo 'checked="checked"'; } ?> />
						</li>
						<li>
							<label for="slider_effects_list">Effects</label>
							<select name="slider_effects_list" id="slider_effects_list">
									<option <?php if($row->slider_list_effects_s == 'none'){ echo 'selected'; } ?>  value="none">None</option>
									<option <?php if($row->slider_list_effects_s == 'cubeH'){ echo 'selected'; } ?>   value="cubeH">Cube Horizontal</option>
									<option <?php if($row->slider_list_effects_s == 'cubeV'){ echo 'selected'; } ?>  value="cubeV">Cube Vertical</option>
									<option <?php if($row->slider_list_effects_s == 'fade'){ echo 'selected'; } ?>  value="fade">Fade</option>
									<option <?php if($row->slider_list_effects_s == 'sliceH'){ echo 'selected'; } ?>  value="sliceH">Slice Horizontal</option>
									<option <?php if($row->slider_list_effects_s == 'sliceV'){ echo 'selected'; } ?>  value="sliceV">Slice Vertical</option>
									<option <?php if($row->slider_list_effects_s == 'slideH'){ echo 'selected'; } ?>  value="slideH">Slide Horizontal</option>
									<option <?php if($row->slider_list_effects_s == 'slideV'){ echo 'selected'; } ?>  value="slideV">Slide Vertical</option>
									<option <?php if($row->slider_list_effects_s == 'scaleOut'){ echo 'selected'; } ?>  value="scaleOut">Scale Out</option>
									<option <?php if($row->slider_list_effects_s == 'scaleIn'){ echo 'selected'; } ?>  value="scaleIn">Scale In</option>
									<option <?php if($row->slider_list_effects_s == 'blockScale'){ echo 'selected'; } ?>  value="blockScale">Block Scale</option>
									<option <?php if($row->slider_list_effects_s == 'kaleidoscope'){ echo 'selected'; } ?>  value="kaleidoscope">Kaleidoscope</option>
									<option <?php if($row->slider_list_effects_s == 'fan'){ echo 'selected'; } ?>  value="fan">Fan</option>
									<option <?php if($row->slider_list_effects_s == 'blindH'){ echo 'selected'; } ?>  value="blindH">Blind Horizontal</option>
									<option <?php if($row->slider_list_effects_s == 'blindV'){ echo 'selected'; } ?>  value="blindV">Blind Vertical</option>
									<option <?php if($row->slider_list_effects_s == 'random'){ echo 'selected'; } ?>  value="random">Random</option>
							</select>
						</li>

						<li>
							<label for="sl_pausetime">Pause time</label>
							<input type="text" name="sl_pausetime" id="sl_pausetime" value="<?php echo $row->description; ?>" class="text_area" />
						</li>
						<li>
							<label for="sl_changespeed">Change speed</label>
							<input type="text" name="sl_changespeed" id="sl_changespeed" value="<?php echo $row->param; ?>" class="text_area" />
						</li>
						<li>
							<label for="slider_position">Slider Position</label>
							<select name="sl_position" id="slider_position">
									<option <?php if($row->sl_position == 'left'){ echo 'selected'; } ?>  value="left">Left</option>
									<option <?php if($row->sl_position == 'right'){ echo 'selected'; } ?>   value="right">Right</option>
									<option <?php if($row->sl_position == 'center'){ echo 'selected'; } ?>  value="center">Center</option>
							</select>
						</li>

					</ul>
					<input type="submit" value="Save Slider" id="save-buttom" class="button button-primary button-large">
					</div>
				</div>
			</form>
<?php
}

function Options_slider_styles()
{
    require_once("slider_Options.php");
    require_once("slider_Options.html.php");
    if (isset($_GET['task']))
        if ($_GET['task'] == 'save')
            save_styles_options();
    showStyles();
}
class Huge_it_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'Huge_it_Widget', 
			'Huge IT Slider', 
			array( 'description' => __( 'Huge IT Slider', 'huge_it_slider' ), ) 
		);
	}
	public function widget( $args, $instance ) {
		extract($args);

		if (isset($instance['slider_id'])) {
			$slider_id = $instance['slider_id'];

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			echo do_shortcode("[huge_it_slider id={$slider_id}]");
			echo $after_widget;
		}
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['slider_id'] = strip_tags( $new_instance['slider_id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}
	public function form( $instance ) {
		$selected_slider = 0;
		$title = "";
		$sliders = false;
		if (isset($instance['slider_id'])) {
			$selected_slider = $instance['slider_id'];
		}
		if (isset($instance['title'])) {
			$title = $instance['title'];
		}
?>
		<p>
			
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<label for="<?php echo $this->get_field_id('slider_id'); ?>"><?php _e('Select Slider:', 'huge_it_slider'); ?></label> 
				<select id="<?php echo $this->get_field_id('slider_id'); ?>" name="<?php echo $this->get_field_name('slider_id'); ?>">
				
				<?php
				 global $wpdb;
				$query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders ";
				$rowwidget=$wpdb->get_results($query);
				foreach($rowwidget as $rowwidgetecho){
				
				selected
				?>
					<option <?php if($rowwidgetecho->id == $instance['slider_id']){ echo 'selected'; } ?> value="<?php echo $rowwidgetecho->id; ?>"><?php echo $rowwidgetecho->name; ?></option>

					<?php } ?>
				</select>

		</p>
		<?php 
	}
}
add_action('widgets_init', 'register_Huge_it_Widget');  
function register_Huge_it_Widget() {  
    register_widget('Huge_it_Widget'); 
}
function huge_it_slider_activate()
{
    global $wpdb;
    $sql_huge_itslider_params = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itslider_params`(
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `name` varchar(50) 
CHARACTER SET utf8 NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
 `description` text CHARACTER SET utf8 NOT NULL,
  `value` varchar(200) CHARACTER SET utf8 NOT NULL,
  
 PRIMARY KEY (`id`)
 
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89 ";

    $sql_huge_itslider_images = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itslider_images` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
 `slider_id` varchar(200) ,
 `description` text,
  `image_url` text,
  `sl_url` varchar(128) DEFAULT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_sl_width` tinyint(4) unsigned DEFAULT NULL,

  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5";

    $sql_huge_itslider_sliders = "
CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "huge_itslider_sliders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sl_height` int(11) unsigned DEFAULT NULL,
  `sl_width` int(11) unsigned DEFAULT NULL,
  `pause_on_hover` text,
  `slider_list_effects_s` text,
  `description` text,
  `param` text,
  `ordering` int(11) NOT NULL,
  `published` text,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
  
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ";

    $table_name = $wpdb->prefix . "huge_itslider_params";
    $sql_1 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
( 'slider_crop_image', 'Slider crop image', 'Slider crop image', 'resize'),
( 'slider_title_color', 'Slider title color', 'Slider title color', '000000'),
( 'slider_title_font_size', 'Slider title font size', 'Slider title font size', '13'),
( 'slider_description_color', 'Slider description color', 'Slider description color', 'ffffff'),
( 'slider_description_font_size', 'Slider description font size', 'Slider description font size', '13'),
( 'slider_title_position', 'Slider title position', 'Slider title position', 'right-top'),
( 'slider_description_position', 'Slider description position', 'Slider description position', 'right-bottom'),
( 'slider_title_border_size', 'Slider Title border size', 'Slider Title border size', '0'),
( 'slider_title_border_color', 'Slider title border color', 'Slider title border color', 'ffffff'),
( 'slider_title_border_radius', 'Slider title border radius', 'Slider title border radius', '4'),
( 'slider_description_border_size', 'Slider description border size', 'Slider description border size', '0'),
( 'slider_description_border_color', 'Slider description border color', 'Slider description border color', 'ffffff'),
( 'slider_description_border_radius', 'Slider description border radius', 'Slider description border radius', '0'),
( 'slider_slideshow_border_size', 'Slider border size', 'Slider border size', '0'),
( 'slider_slideshow_border_color', 'Slider border color', 'Slider border color', 'ffffff'),
( 'slider_slideshow_border_radius', 'Slider border radius', 'Slider border radius', '0'),
( 'slider_navigation_type', 'Slider navigation type', 'Slider navigation type', '1'),
( 'slider_navigation_position', 'Slider navigation position', 'Slider navigation position', 'bottom'),
( 'slider_title_background_color', 'Slider title background color', 'Slider title background color', 'ffffff'),
( 'slider_description_background_color', 'Slider description background color', 'Slider description background color', '000000'),
( 'slider_title_transparent', 'Slider title has background', 'Slider title has background', 'on'),
( 'slider_description_transparent', 'Slider description has background', 'Slider description has background', 'on'),
( 'slider_slider_background_color', 'Slider slider background color', 'Slider slider background color', 'ffffff'),
( 'slider_dots_position', 'slider dots position', 'slider dots position', 'top'),
( 'slider_active_dot_color', 'slider active dot color', '', 'ffffff'),
( 'slider_dots_color', 'slider dots color', '', '000000');


query1;

    $table_name = $wpdb->prefix . "huge_itslider_images";
    $sql_2 = "
INSERT INTO 

`" . $table_name . "` (`id`, `slider_id`, `name`, `description`, `image_url`, `sl_url`, `ordering`, `published`) VALUES
(1, '1', '',  '', '" . plugins_url("Front_images/slides/slide1.jpg", __FILE__) . "', 'http://huge-it.com',  1, 1),
(2, '1', 'Simple Usage',  '', '" . plugins_url("Front_images/slides/slide2.jpg", __FILE__) . "', 'http://huge-it.com',  2, 1),
(3, '1', 'Huge-IT Slider',  'The slider allows having unlimited amount of images with their titles and descriptions. The slider uses autogenerated shortcodes making it easier for the users to add it to the custom location.', '" . plugins_url("Front_images/slides/slide3.jpg", __FILE__) . "', 'http://huge-it.com',  3, 1)";

    $table_name = $wpdb->prefix . "huge_itslider_sliders";

    $sql_3 = "

INSERT INTO `$table_name` (`id`, `name`, `sl_height`, `sl_width`, `pause_on_hover`, `slider_list_effects_s`, `description`, `param`, `ordering`, `published`) VALUES
(1, 'My First Slider', '375', '600', 'on', 'random', '4000', '1000', '1', '300')";

    $wpdb->query($sql_huge_itslider_params);
    $wpdb->query($sql_huge_itslider_images);
    $wpdb->query($sql_huge_itslider_sliders);

    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itslider_params")) {
        $wpdb->query($sql_1);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itslider_images")) {
      $wpdb->query($sql_2);
    }
    if (!$wpdb->get_var("select count(*) from " . $wpdb->prefix . "huge_itslider_sliders")) {
      $wpdb->query($sql_3);
    }

	$product = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_sliders", ARRAY_A);
    $isUpdate = 0;
	foreach ($product as $prod) {
        if ($prod['Field'] == 'published' && $prod['Type'] == 'tinyint(4) unsigned') {
            $isUpdate = 1;
            break;
        }
    }
	if ($isUpdate) {
	$wpdb->query("ALTER TABLE `".$wpdb->prefix."huge_itslider_sliders` MODIFY `published` text");
	$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET published = '300' WHERE id = 1 ");
	}
	
	$product2 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_images", ARRAY_A);
    $isUpdate2 = 0;
	foreach ($product2 as $prod2) {

			if($product2[6]['Field'] == 'sl_type')
			{
			echo '';
			}
			else
			{
			$query="SELECT * FROM ".$wpdb->prefix."huge_itslider_images order by id ASC";
			   $rowim=$wpdb->get_results($query);
	  foreach ($rowim as $key=>$rowimages){
	  $wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_images SET  ordering = '".$rowimages->id."'  WHERE ID = ".$rowimages->id." ");
	  }
			}
    }
		if($product2[6]['Field'] == 'sl_type')
			{
			echo '';
			}
			else
			{
			$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_images` ADD  `sl_type` TEXT NOT NULL AFTER  `sl_url`");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_images SET sl_type = 'image' ");
			$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_images` ADD  `link_target` TEXT NOT NULL AFTER  `sl_type`");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_images SET link_target = 'on' ");

		    $table_name = $wpdb->prefix . "huge_itslider_params";
    $sql_update2 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
( 'slider_description_width', 'Slider description width', 'Slider description width', '70'),
( 'slider_description_height', 'Slider description height', 'Slider description height', '50'),
( 'slider_description_background_transparency', 'slider description background transparency', 'slider description background transparency', '70'),
( 'slider_description_text_align', 'description text-align', 'description text-align', 'justify'),
( 'slider_title_width', 'slider title width', 'slider title width', '30'),
( 'slider_title_height', 'slider title height', 'slider title height', '50'),
( 'slider_title_background_transparency', 'slider title background transparency', 'slider title background transparency', '70'),
( 'slider_title_text_align', 'title text-align', 'title text-align', 'right');

query1;
			 $wpdb->query($sql_update2);
	}
	$product3 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_sliders", ARRAY_A);
	if($product3[8]['Field'] == 'sl_position'){
		echo '';
	}
	else
	{
	$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_sliders` ADD  `sl_position` TEXT NOT NULL AFTER  `param`");
	$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET `sl_position` = 'center' ");
	$table_name = $wpdb->prefix . "huge_itslider_params";
    $sql_update3 = <<<query1
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
( 'slider_title_has_margin', 'title has margin', 'title has margin', 'on'),
( 'slider_description_has_margin', 'description has margin', 'description has margin', 'on'),
( 'slider_show_arrows', 'Slider show left right arrows', 'Slider show left right arrows', 'on');

query1;
	 $wpdb->query($sql_update3);
	}
		$product4 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_images", ARRAY_A);
	if($product4[8]['Field'] == 'sl_stitle'){
		echo '';
	}
	else
	{
		$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_images` ADD  `sl_stitle` TEXT NOT NULL AFTER  `link_target`");
		$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_images` ADD  `sl_sdesc` TEXT NOT NULL AFTER  `sl_stitle`");
		$wpdb->query("ALTER TABLE  `".$wpdb->prefix."huge_itslider_images` ADD  `sl_postlink` TEXT NOT NULL AFTER  `sl_sdesc`");
	}	
}
register_activation_hook(__FILE__, 'huge_it_slider_activate');