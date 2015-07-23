<?php

/*
Plugin Name: Huge IT Slider
Plugin URI: http://huge-it.com/slider
Description: Huge IT slider is a convenient tool for organizing the images represented on your website into sliders. Each product on the slider is assigned with a relevant slider, which makes it easier for the customers to search and identify the needed images within the slider.
Version: 2.8.7
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

function remove_media_tab($strings) {

	//unset($strings["insertFromUrlTitle"]);
	return $strings;
}
add_filter('media_view_strings','remove_media_tab');


add_action('init', 'hugesl_do_output_buffer');
function hugesl_do_output_buffer() {
        ob_start();
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
	add_style_to_header($atts['id']);
	add_action('wp_head', 'add_style_to_header');
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

add_action('admin_menu', 'huge_it_slider_options_panel');
function huge_it_slider_options_panel()
{
    $page_cat = add_menu_page('Theme page title', 'Huge IT Slider', 'delete_pages', 'sliders_huge_it_slider', 'sliders_huge_it_slider', plugins_url('images/sidebar.icon.png', __FILE__));
    add_submenu_page('sliders_huge_it_slider', 'Sliders', 'Sliders', 'delete_pages', 'sliders_huge_it_slider', 'sliders_huge_it_slider');
    $page_option = add_submenu_page('sliders_huge_it_slider', 'General Options', 'General Options', 'manage_options', 'Options_slider_styles', 'Options_slider_styles');
	add_submenu_page( 'sliders_huge_it_slider', 'Licensing', 'Licensing', 'manage_options', 'huge_it_slider_Licensing', 'huge_it_slider_Licensing');
	add_submenu_page('sliders_huge_it_slider', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'huge_it_slider_featured_plugins', 'huge_it_slider_featured_plugins');
	
	add_action('admin_print_styles-' . $page_cat, 'huge_it_slider_admin_script');
    add_action('admin_print_styles-' . $page_option, 'huge_it_slider_option_admin_script');
	
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
function huge_it_slider_featured_plugins()
{
		?>
<style>
.element {
	position: relative;
	width:93%; 
	margin:5px 0px 5px 0px;
	padding:2%;
	clear:both;
	overflow: hidden;
	border:1px solid #DEDEDE;
	background:#F9F9F9;
}
.element > div {
	display:table-cell;
}
.element div.left-block {
	padding-right:10px;
}
.element div.left-block .main-image-block {
	clear:both; 
}
.element div.left-block .thumbs-block {
	position:relative;
	margin-top:10px;
}
.element div.left-block .thumbs-block ul {
	width:240px; 
	height:auto;
	display:table;
	margin:0px;
	padding:0px;
	list-style:none;
}
.element div.left-block .thumbs-block ul li {
	margin:0px 3px 0px 2px;
	padding:0px;
	width:75px; 
	height:75px; 
	float:left;
}
.element div.left-block .thumbs-block ul li a {
	display:block;
	width:75px; 
	height:75px; 
}
.element div.left-block .thumbs-block ul li a img {
	width:75px; 
	height:75px; 
}
.element div.right-block {
	vertical-align:top;
}
.element div.right-block > div {
	width:100%;
	padding-bottom:10px;
	margin-top:10px;
}
.element div.right-block > div:last-child {
	background:none;
}
.element div.right-block .title-block  {
	margin-top:3px;
}
.element div.right-block .title-block h3 {
	margin:0px;
	padding:0px;
	font-weight:normal;
	font-size:18px !important;
	line-height:18px !important;
	color:#0074A2;
}
.element div.right-block .description-block p,.element div.right-block .description-block * {
	margin:0px;
	padding:0px;
	font-weight:normal;
	font-size:14px;
	color:#555555;
}
.element div.right-block .description-block h1,
.element div.right-block .description-block h2,
.element div.right-block .description-block h3,
.element div.right-block .description-block h4,
.element div.right-block .description-block h5,
.element div.right-block .description-block h6,
.element div.right-block .description-block p, 
.element div.right-block .description-block strong,
.element div.right-block .description-block span {
	padding:2px !important;
	margin:0px !important;
}
.element div.right-block .description-block ul,
.element div.right-block .description-block li {
	padding:2px 0px 2px 5px;
	margin:0px 0px 0px 8px;
}
.element .button-block {
	position:relative;
}
.element div.right-block .button-block a,.element div.right-block .button-block a:link,.element div.right-block .button-block a:visited {
	position:relative;
	display:inline-block;
	padding:6px 12px;
	background:#2EA2CD;
	color:#FFFFFF;
	font-size:14;
	text-decoration:none;
}
.element div.right-block .button-block a:hover,.pupup-elemen.element div.right-block .button-block a:focus,.element div.right-block .button-block a:active {
	background:#0074A2;
	color:#FFFFFF;
}
.button-block a {
	float: right;
}
.description-block p {
	text-align: justify !important;
}
@media only screen and (max-width: 767px) {
	.element > div {
		display:block;
		width:100%;
		clear:both;
	}
	.element div.left-block {
		padding-right:0px;
	}
	.element div.left-block .main-image-block {
		clear:both;
		width:100%; 
	}
	.element div.left-block .main-image-block img {
		width:100% !important;  
		height:auto;
	}
	.element div.left-block .thumbs-block ul {
		width:100%; 
	}
}
</style>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/image-gallery-icon.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/image-gallery-icon.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Image Gallery</h3></div>
		<div class="description-block">
			<p>Huge-IT Gallery images is perfect for using for creating various galleries within various views, to creating various sliders with plenty of styles, beautiful lightboxes with it’s options for any taste. The product allows adding descriptions and titles for each image of the Gallery. It is rather useful wherever using with various pages and posts, as well as within custom location.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/wordpress-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/potfolio-gallery-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/potfolio-gallery-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Portfolio/Gallery</h3></div>
		<div class="description-block">
			<p>Portfolio Gallery is perfect for using for creating various portfolios or gallery within various views. The product allows adding descriptions and titles for each portfolio gallery. It is rather useful whever using with various pages and posts, as well as within custom location.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/portfolio-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/video-gallery-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/video-gallery-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Video Gallery</h3></div>
		<div class="description-block">
			<p>Video Gallery plugin was created and specifically designed to show your video files in unusual splendid ways. It has 5 good-looking views. Each are made in different taste so that you can choose any of them, according to the style of your website.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/video-gallery/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/lightbox-logo.jpg' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/lightbox-logo.jpg' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Lightbox</h3></div>
		<div class="description-block">
			<p>Lightbox is a perfect tool for viewing photos. It is created especially for simplification of using, permits you to view larger version of images and giving an interesting design. With the help of slideshow and various styles, betray a unique image to your website.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/lightbox/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/product-catalog-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/product-catalog-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>WordPress Product Catalog</h3></div>
		<div class="description-block">
			<p>Huge-IT Product Catalog is made for demonstration, sale, advertisements for your products. Imagine a stand with a variety of catalogs with a specific product category. To imagine is not difficult, to use is even easier.</p>
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/product-catalog/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/share-buttons-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/share-buttons-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Share Buttons</h3></div>
			<p>Social network is one of the popular places where people get information about everything in the world. Adding social share buttons into your blog or website page is very necessary and useful element for "socialization" of the project.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/share-buttons/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/google-maps-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/google-maps-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Google Map</h3></div>
			<p>Huge-IT Google Map. One more perfect tool from Huge-IT. Improved Google Map, where we have our special contribution. Most simple and effective tool for rapid creation of individual Google Map in posts and pages.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/google-map/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
<div class="element hugeitmicro-item">
	<div class="left-block">
		<div class="main-image-block">
			<a href="<?php echo plugins_url( 'images/video-player-logo.png' , __FILE__ ); ?>" rel="content"><img src="<?php echo plugins_url( 'images/video-player-logo.png' , __FILE__ ); ?>"></a>
		</div>
	</div>
	<div class="right-block">
		<div class="title-block"><h3>Wordpress Video Player</h3></div>
			<p>Inserting video on a page is a perfect way to supplement website with media content and expand the user’s interest in your site. Huge-IT Video Player is extremely necessary video tool for your sites, which provides a wide range of different file formats.</p>
		<div class="description-block">
		</div>			  				
		<div class="button-block">
			<a href="http://huge-it.com/video-player/" target="_blank">View Plugin</a>
		</div>
	</div>
</div>
		<?php
}

function huge_it_slider_admin_script()
{
		wp_enqueue_media();
		wp_enqueue_style("jquery_ui", "http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css", FALSE);
		if ( !defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			wp_enqueue_script("jquery_new", "http://code.jquery.com/jquery-1.10.2.js", FALSE);
			wp_enqueue_script("jquery_ui_new", "http://code.jquery.com/ui/1.10.4/jquery-ui.js", FALSE);
		}
		
		wp_enqueue_script("simple_slider_js",  plugins_url("js/simple-slider.js", __FILE__), FALSE);
		wp_enqueue_style("simple_slider_css", plugins_url("style/simple-slider.css", __FILE__), FALSE);
		wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
		wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
		wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
}

function huge_it_slider_option_admin_script()
{

		wp_enqueue_script("jquery_old", "http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js", FALSE);
	if (!wp_script_is( 'jQuery.ui' )) 
{
       // wp_enqueue_script("jquery_new", "http://code.jquery.com/jquery-1.10.2.js", FALSE);
        wp_enqueue_script("jquery_ui_new", "http://code.jquery.com/ui/1.10.4/jquery-ui.js", FALSE);
}
		wp_enqueue_script("simple_slider_js",  plugins_url("js/simple-slider.js", __FILE__), FALSE);
		wp_enqueue_style("simple_slider_css", plugins_url("style/simple-slider.css", __FILE__), FALSE);
		wp_enqueue_style("admin_css", plugins_url("style/admin.style.css", __FILE__), FALSE);
		wp_enqueue_script("admin_js", plugins_url("js/admin.js", __FILE__), FALSE);
		wp_enqueue_script('param_block2', plugins_url("elements/jscolor/jscolor.js", __FILE__));
	
if (!wp_script_is( 'jQuery.ui' ))
{
       // wp_enqueue_script("jquery_new", "http://code.jquery.com/jquery-1.10.2.js", FALSE);
        wp_enqueue_script("jquery_ui_new", "http://code.jquery.com/ui/1.10.4/jquery-ui.js", FALSE);
}
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
            break;
		case 'popup_video':
            if ($id)
                popup_video($id);
            else {
                $id = $wpdb->get_var("SELECT MAX( id ) FROM " . $wpdb->prefix . "huge_itslider_sliders");
                popup_video($id);
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
#wpadminbar,.auto-fold #adminmenu, .auto-fold #adminmenu li.menu-top, .auto-fold #adminmenuback, .auto-fold #adminmenuwrap {
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
 
h3 {
	margin:30px 0px 15px 0px;
}
</style>
<div class="clear"></div>
<h3>Select the Slider</h3>
<div id="huge_it_slider">
  <?php 
  	  global $wpdb;
	  session_start();
	  $query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders";
	  $firstrow=$wpdb->get_row($query);
	  if(isset($_POST["hugeit_slider_id"])){
		  $_POST["hugeit_slider_id"] = esc_html($_POST["hugeit_slider_id"]);
	  $id=$_POST["hugeit_slider_id"];
	  }
	  else{
	  $id=$firstrow->id;
	  }
	  	if(isset($_REQUEST['csrf_token_hugeit_1753'])){
	$_REQUEST['csrf_token_hugeit_1753'] = esc_html($_REQUEST['csrf_token_hugeit_1753']);
	if($_SESSION['csrf_token_hugeit_1753'] == $_REQUEST['csrf_token_hugeit_1753']){
	  if(isset($_GET["htslider_id"]) && $_GET["htslider_id"] == $_POST["hugeit_slider_id"]){
              if($_GET["hugeit_save"]==1){
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_width = '%s'  WHERE id = %d ", $_POST["sl_width"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_height = '%s'  WHERE id = %d ", $_POST["sl_height"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  pause_on_hover = '%s'  WHERE id = %d ", $_POST["pause_on_hover"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  slider_list_effects_s = '%s'  WHERE id = %d ", $_POST["slider_effects_list"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  description = '%s'  WHERE id = %d ", $_POST["sl_pausetime"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  param = '%s'  WHERE id = %d ", $_POST["sl_changespeed"], $id));
                  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_position = '%s'  WHERE id = %d ", $_POST["sl_position"], $id));
				  $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_sliders SET  sl_loading_icon = '%s' WHERE id = %d ", $_POST["sl_loading_icon"], $id));

              }
          }
		  }
	  }
	  $query="SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders order by id ASC";
			   $shortcodesliders=$wpdb->get_results($query);
		$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders WHERE id= %d", $id);
	   $row=$wpdb->get_row($query);$container_id = 'huge_it_slider';
	  
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
					<input type="hidden" name="csrf_token_hugeit_1753" value="csrf_token_hugeit_1753" />
					 <?php			
						$_SESSION['csrf_token_hugeit_1753'] = 'csrf_token_hugeit_1753';
					?>
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

/**
 * Huge IT Widget
 */
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
/***<add>***/



function add_style_to_header($id) {
	global $wpdb;
 	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itslider_images where slider_id = '%d' order by ordering ASC",$id);
	$images=$wpdb->get_results($query);
	$query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."huge_itslider_sliders where id = '%d' order by id ASC",$id);
	$slider=$wpdb->get_results($query);
	$query="SELECT * FROM ".$wpdb->prefix."huge_itslider_params";
    $rowspar = $wpdb->get_results($query);
    $paramssld = array();
    foreach ($rowspar as $rowpar) {
        $key = $rowpar->name;
        $value = $rowpar->value;
        $paramssld[$key] = $value;
    }
	$sliderID=$slider[0]->id;
	$slidertitle=$slider[0]->name;
	$sliderheight=$slider[0]->sl_height;
	$sliderwidth=$slider[0]->sl_width;
	$slidereffect=$slider[0]->slider_list_effects_s;
	$slidepausetime=($slider[0]->description+$slider[0]->param);
	$sliderpauseonhover=$slider[0]->pause_on_hover;
	$sliderposition=$slider[0]->sl_position;
	$slidechangespeed=$slider[0]->param;
	$sliderloadingicon=$slider[0]->sl_loading_icon;
	$slideshow_title_position = explode('-', trim($paramssld['slider_title_position']));
	$slideshow_description_position = explode('-', trim($paramssld['slider_description_position']));
	?>
	<style>			
	  #huge_it_loading_image_<?php echo $sliderID; ?> {
		height:<?php echo $sliderheight; ?>px;
		width:<?php  echo $sliderwidth; ?>px;
		display: table-cell;
		text-align: center;
		vertical-align: middle;
	 }
	  #huge_it_loading_image_<?php echo $sliderID; ?>.display {
		display: table-cell;
	 }
	  #huge_it_loading_image_<?php echo $sliderID; ?>.nodisplay {
		display: none;
	 }
	 #huge_it_loading_image_<?php echo $sliderID; ?> img {
		margin: auto 0;
		width: 20% !important;
		
	 }
	 
	 .huge_it_slideshow_image_wrap_<?php echo $sliderID; ?> {
		height:<?php echo $sliderheight ; ?>px;
		width:<?php  echo $sliderwidth ; ?>px;
		position:relative;
		display: block;
		text-align: center;
		/*HEIGHT FROM HEADER.PHP*/
		clear:both;
		<?php if($sliderposition=="left"){ $position='float:left;';}elseif($sliderposition=="right"){$position='float:right;';}else{$position='float:none; margin:0px auto;';} ?>
		<?php echo $position;  ?>
		
		border-style:solid;
		border-left:0px !important;
		border-right:0px !important;
	}
	 .huge_it_slideshow_image_wrap1_<?php echo $sliderID; ?>.display {
		 width: 100%;
		 height:100%;
	 }
	 .huge_it_slideshow_image_wrap1_<?php echo $sliderID; ?>.display {
		 display:block;
	 }
	 .huge_it_slideshow_image_wrap1_<?php echo $sliderID; ?>.nodisplay {
		 display:none;
	 }
	.huge_it_slideshow_image_wrap_<?php echo $sliderID; ?> * {
		box-sizing: border-box;
		-moz-box-sizing: border-box;
		-webkit-box-sizing: border-box;
	}
		 

	  .huge_it_slideshow_image_<?php echo $sliderID; ?> {
			/*width:100%;*/
	  }

	  #huge_it_slideshow_left_<?php echo $sliderID; ?>,
	  #huge_it_slideshow_right_<?php echo $sliderID; ?> {
		cursor: pointer;
		display:none;
		display: block;
		
		height: 100%;
		outline: medium none;
		position: absolute;

		/*z-index: 10130;*/
		z-index: 13;
		bottom:25px;
		top:50%;		
	  }
	 

	  #huge_it_slideshow_left-ico_<?php echo $sliderID; ?>,
	  #huge_it_slideshow_right-ico_<?php echo $sliderID; ?> {
		z-index: 13;
		-moz-box-sizing: content-box;
		box-sizing: content-box;
		cursor: pointer;
		display: table;
		left: -9999px;
		line-height: 0;
		margin-top: -15px;
		position: absolute;
		top: 50%;
		/*z-index: 10135;*/
	  }
	  #huge_it_slideshow_left-ico_<?php echo $sliderID; ?>:hover,
	  #huge_it_slideshow_right-ico_<?php echo $sliderID; ?>:hover {
		cursor: pointer;
	  }
	  
	  .huge_it_slideshow_image_container_<?php echo $sliderID; ?> {
		display: table;
		position: relative;
		top:0px;
		left:0px;
		text-align: center;
		vertical-align: middle;
		width:100%;
	  }	  
		
	  .huge_it_slideshow_title_text_<?php echo $sliderID; ?> {
		text-decoration: none;
		position: absolute;
		z-index: 11;
		display: inline-block;
		<?php  if($paramssld['slider_title_has_margin']=='on'){
				$slider_title_width=($paramssld['slider_title_width']-6);
				$slider_title_height=($paramssld['slider_title_height']-6);
				$slider_title_margin="3";
			}else{
				$slider_title_width=($paramssld['slider_title_width']);
				$slider_title_height=($paramssld['slider_title_height']);
				$slider_title_margin="0";
			}  ?>
		
		width:<?php echo $slider_title_width; ?>%;
		/*height:<?php echo $slider_title_height; ?>%;*/
		
		<?php 
			if($slideshow_title_position[0]=="left"){echo 'left:'.$slider_title_margin.'%;';}
			elseif($slideshow_title_position[0]=="center"){echo 'left:50%;';}
			elseif($slideshow_title_position[0]=="right"){echo 'right:'.$slider_title_margin.'%;';}
			
			if($slideshow_title_position[1]=="top"){echo 'top:'.$slider_title_margin.'%;';}
			elseif($slideshow_title_position[1]=="middle"){echo 'top:50%;';}
			elseif($slideshow_title_position[1]=="bottom"){echo 'bottom:'.$slider_title_margin.'%;';}
		 ?>
		padding:2%;
		text-align:<?php echo $paramssld['slider_title_text_align']; ?>;  
		font-weight:bold;
		color:#<?php echo $paramssld['slider_title_color']; ?>;
			
		background:<?php 			
				list($r,$g,$b) = array_map('hexdec',str_split($paramssld['slider_title_background_color'],2));
				$titleopacity=$paramssld["slider_title_background_transparency"]/100;						
				echo 'rgba('.$r.','.$g.','.$b.','.$titleopacity.')  !important'; 		
		?>;
		border-style:solid;
		font-size:<?php echo $paramssld['slider_title_font_size']; ?>px;
		border-width:<?php echo $paramssld['slider_title_border_size']; ?>px;
		border-color:#<?php echo $paramssld['slider_title_border_color']; ?>;
		border-radius:<?php echo $paramssld['slider_title_border_radius']; ?>px;
	  }
	  	  
	  .huge_it_slideshow_description_text_<?php echo $sliderID; ?> {
		text-decoration: none;
		position: absolute;
		z-index: 11;
		border-style:solid;
		display: inline-block;
		<?php  if($paramssld['slider_description_has_margin']=='on'){
				$slider_description_width=($paramssld['slider_description_width']-6);
				$slider_description_height=($paramssld['slider_description_height']-6);
				$slider_description_margin="3";
			}else{
				$slider_description_width=($paramssld['slider_description_width']);
				$slider_descriptione_height=($paramssld['slider_description_height']);
				$slider_description_margin="0";
			}  ?>
		
		width:<?php echo $slider_description_width; ?>%;
		/*height:<?php echo $slider_description_height; ?>%;*/
		<?php 
			if($slideshow_description_position[0]=="left"){echo 'left:'.$slider_description_margin.'%;';}
			elseif($slideshow_description_position[0]=="center"){echo 'left:50%;';}
			elseif($slideshow_description_position[0]=="right"){echo 'right:'.$slider_description_margin.'%;';}
			
			if($slideshow_description_position[1]=="top"){echo 'top:'.$slider_description_margin.'%;';}
			elseif($slideshow_description_position[1]=="middle"){echo 'top:50%;';}
			elseif($slideshow_description_position[1]=="bottom"){echo 'bottom:'.$slider_description_margin.'%;';}
		 ?>
		padding:3%;
		text-align:<?php echo $paramssld['slider_description_text_align']; ?>;  
		color:#<?php echo $paramssld['slider_description_color']; ?>;
		
		background:<?php 
			list($r,$g,$b) = array_map('hexdec',str_split($paramssld['slider_description_background_color'],2));	
			$descriptionopacity=$paramssld["slider_description_background_transparency"]/100;
			echo 'rgba('.$r.','.$g.','.$b.','.$descriptionopacity.') !important';
		?>;
		border-style:solid;
		font-size:<?php echo $paramssld['slider_description_font_size']; ?>px;
		border-width:<?php echo $paramssld['slider_description_border_size']; ?>px;
		border-color:#<?php echo $paramssld['slider_description_border_color']; ?>;
		border-radius:<?php echo $paramssld['slider_description_border_radius']; ?>px;
	  }
	  
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?>.none, .huge_it_slideshow_description_text_<?php echo $sliderID; ?>.none,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?>.hidden, .huge_it_slideshow_description_text_<?php echo $sliderID; ?>.hidden	   {display:none;}
	      
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h1, .huge_it_slideshow_description_text_<?php echo $sliderID; ?> h1,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h2, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h2,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h3, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h3,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h4, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> h4,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> p, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> p,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> strong,  .huge_it_slideshow_title_text_<?php echo $sliderID; ?> strong,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> span, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> span,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> ul, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> ul,
	   .huge_it_slideshow_title_text_<?php echo $sliderID; ?> li, .huge_it_slideshow_title_text_<?php echo $sliderID; ?> li {
			padding:2px;
			margin:0px;
	   }

	  .huge_it_slide_container_<?php echo $sliderID; ?> {
		display: table-cell;
		margin: 0 auto;
		position: relative;
		vertical-align: middle;
		width:100%;
		height:100%;
		_width: inherit;
		_height: inherit;
	  }
	  .huge_it_slide_bg_<?php echo $sliderID; ?> {
		margin: 0 auto;
		width:100%;
		height:100%;
		_width: inherit;
		_height: inherit;
	  }
	  .huge_it_slider_<?php echo $sliderID; ?> {
		width:100%;
		height:100%;
		display:table !important;
		padding:0px !important;
		margin:0px !important;
		
	  }
	  .huge_it_slideshow_image_item_<?php echo $sliderID; ?> {
		width:100%;
		height:100%;
		_width: inherit;
		_height: inherit;
		display: table-cell;
		filter: Alpha(opacity=100);
		opacity: 1;
		position: absolute;
		top:0px !important;
		left:0px !important;
		vertical-align: middle;
		z-index: 1;
		margin:0px !important;
		padding:0px  !important;
		border-radius: <?php echo $paramssld['slider_slideshow_border_radius']; ?>px !important;
	  }
	  .huge_it_slideshow_image_second_item_<?php echo $sliderID; ?> {
		width:100%;
		height:100%;
		_width: inherit;
		_height: inherit;
		display: table-cell;
		filter: Alpha(opacity=0);
		opacity: 0;
		position: absolute;
		top:0px !important;
		left:0px !important;
		vertical-align: middle;
		overflow:hidden;
		margin:0px !important;
		visibility:visible !important;
		padding:0px  !important;
		border-radius: <?php echo $paramssld['slider_slideshow_border_radius']; ?>px !important;
	  }
	  
	   .huge_it_slideshow_image_second_item_<?php echo $sliderID; ?> a, .huge_it_slideshow_image_item_<?php echo $sliderID; ?> a {
			display:block;
			width:100%;
			height:100%;	
	   }
	   
	  .huge_it_grid_<?php echo $sliderID; ?> {
		display: none;
		height: 100%;
		overflow: hidden;
		position: absolute;
		width: 100%;
	  }
	  .huge_it_gridlet_<?php echo $sliderID; ?> {
		opacity: 1;
		filter: Alpha(opacity=100);
		position: absolute;
	  }
	  
					
	  .huge_it_slideshow_dots_container_<?php echo $sliderID; ?> {
		display: table;
		position: absolute;
		width:100% !important;
		height:100% !important;
	  }
	  .huge_it_slideshow_dots_thumbnails_<?php echo $sliderID; ?> {
		margin: 0 auto;
		overflow: hidden;
		position: absolute;
		width:100%;
		height:30px;
	  }
	  
	  .huge_it_slideshow_dots_<?php echo $sliderID; ?> {
		display: inline-block;
		position: relative;
		cursor: pointer;
		box-shadow: 1px 1px 1px rgba(0,0,0,0.1) inset, 1px 1px 1px rgba(255,255,255,0.1);
		width:10px;
		height: 10px;
		border-radius: 10px;
		background: #00f;
		margin: 10px;
		overflow: hidden;
		z-index: 17;
	  }
	  
	  .huge_it_slideshow_dots_active_<?php echo $sliderID; ?> {
		opacity: 1;
		filter: Alpha(opacity=100);
	  }
	  .huge_it_slideshow_dots_deactive_<?php echo $sliderID; ?> {
	  
	  }
	  
	
		
		.huge_it_slideshow_image_wrap_<?php echo $sliderID; ?> {
			background:#<?php echo $paramssld['slider_slider_background_color']; ?>;
			border-width:<?php echo $paramssld['slider_slideshow_border_size']; ?>px;
			border-color:#<?php echo $paramssld['slider_slideshow_border_color']; ?>;
			border-radius:<?php echo $paramssld['slider_slideshow_border_radius']; ?>px;
		}
		.huge_it_slideshow_image_wrap_<?php echo $sliderID; ?>.nocolor {
			background: transparent;
		}
		
		.huge_it_slideshow_dots_thumbnails_<?php echo $sliderID; ?> {
			<?php if($paramssld['slider_dots_position']=="bottom"){?>
			bottom:0px;
			<?php }else if($paramssld['slider_dots_position']=="none"){?>
			display:none;
			<?php
			}else{ ?>
			top:0px; <?php } ?>
		}
		
		.huge_it_slideshow_dots_<?php echo $sliderID; ?> {
			background:#<?php echo $paramssld['slider_dots_color']; ?>;
		}
		
		.huge_it_slideshow_dots_active_<?php echo $sliderID; ?> {
			background:#<?php echo $paramssld['slider_active_dot_color']; ?>;
		}
		
		<?php
		
		$arrowfolder=plugins_url('slider-image/Front_images/arrows');
		switch ($paramssld['slider_navigation_type']) {
			case 1:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-21px;
						height:43px;
						width:29px;
						background:url(<?php echo $arrowfolder;?>/arrows.simple.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
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
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.shadow.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.shadow.png) right top no-repeat; 
					}

					#huge_it_slideshow_left_<?php echo $sliderID; ?>:hover {
						background-position:left -50px;
					}

					#huge_it_slideshow_right_<?php echo $sliderID; ?>:hover {
						background-position:right -50px;
					}
				<?php
				break;
			case 3:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-22px;
						height:44px;
						width:44px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.simple.dark.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-22px;
						height:44px;
						width:44px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.simple.dark.png) right top no-repeat; 
					}

					#huge_it_slideshow_left_<?php echo $sliderID; ?>:hover {
						background-position:left -44px;
					}

					#huge_it_slideshow_right_<?php echo $sliderID; ?>:hover {
						background-position:right -44px;
					}
				<?php
				break;
			case 4:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-33px;
						height:65px;
						width:59px;
						background:url(<?php echo $arrowfolder;?>/arrows.cube.dark.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-33px;
						height:65px;
						width:59px;
						background:url(<?php echo $arrowfolder;?>/arrows.cube.dark.png) right top no-repeat; 
					}

					#huge_it_slideshow_left_<?php echo $sliderID; ?>:hover {
						background-position:left -66px;
					}

					#huge_it_slideshow_right_<?php echo $sliderID; ?>:hover {
						background-position:right -66px;
					}
				<?php
				break;
			case 5:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-18px;
						height:37px;
						width:40px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.blue.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
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
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.cube.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-25px;
						height:50px;
						width:50px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.cube.png) right top no-repeat; 
					}

					#huge_it_slideshow_left_<?php echo $sliderID; ?>:hover {
						background-position:left -50px;
					}

					#huge_it_slideshow_right_<?php echo $sliderID; ?>:hover {
						background-position:right -50px;
					}
				<?php
				break;
			case 7:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						right:0px;
						margin-top:-19px;
						height:38px;
						width:38px;
						background:url(<?php echo $arrowfolder;?>/arrows.light.transparent.circle.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
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
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
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
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-22px;
						height:45px;
						width:45px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.blue.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
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
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-24px;
						height:48px;
						width:48px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.green.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-24px;
						height:48px;
						width:48px;
						background:url(<?php echo $arrowfolder;?>/arrows.circle.green.png) right top no-repeat; 
					}

					#huge_it_slideshow_left_<?php echo $sliderID; ?>:hover {
						background-position:left -48px;
					}

					#huge_it_slideshow_right_<?php echo $sliderID; ?>:hover {
						background-position:right -48px;
					}
				<?php
				break;
			case 11:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-29px;
						height:58px;
						width:55px;
						background:url(<?php echo $arrowfolder;?>/arrows.blue.retro.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-29px;
						height:58px;
						width:55px;
						background:url(<?php echo $arrowfolder;?>/arrows.blue.retro.png) right top no-repeat; 
					}
				<?php
				break;
			case 12:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-37px;
						height:74px;
						width:74px;
						background:url(<?php echo $arrowfolder;?>/arrows.green.retro.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-37px;
						height:74px;
						width:74px;
						background:url(<?php echo $arrowfolder;?>/arrows.green.retro.png) right top no-repeat; 
					}
				<?php
				break;
			case 13:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-16px;
						height:33px;
						width:33px;
						background:url(<?php echo $arrowfolder;?>/arrows.red.circle.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-16px;
						height:33px;
						width:33px;
						background:url(<?php echo $arrowfolder;?>/arrows.red.circle.png) right top no-repeat; 
					}
				<?php
				break;
			case 14:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-51px;
						height:102px;
						width:52px;
						background:url(<?php echo $arrowfolder;?>/arrows.triangle.white.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-51px;
						height:102px;
						width:52px;
						background:url(<?php echo $arrowfolder;?>/arrows.triangle.white.png) right top no-repeat; 
					}
				<?php
				break;
			case 15:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:0px;
						margin-top:-19px;
						height:39px;
						width:70px;
						background:url(<?php echo $arrowfolder;?>/arrows.ancient.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:0px;
						margin-top:-19px;
						height:39px;
						width:70px;
						background:url(<?php echo $arrowfolder;?>/arrows.ancient.png) right top no-repeat; 
					}
				<?php
				break;
			case 16:
				?>
					#huge_it_slideshow_left_<?php echo $sliderID; ?> {	
						left:-21px;
						margin-top:-20px;
						height:40px;
						width:37px;
						background:url(<?php echo $arrowfolder;?>/arrows.black.out.png) left  top no-repeat; 
					}
					
					#huge_it_slideshow_right_<?php echo $sliderID; ?> {
						right:-21px;
						margin-top:-20px;
						height:40px;
						width:37px;
						background:url(<?php echo $arrowfolder;?>/arrows.black.out.png) right top no-repeat; 
					}
				<?php
				break;
		}
?>
	</style>
<?php }
/***</add>***/
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
  //  $wpdb->query($sql_huge_itslider_sliders_update);


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
	$wpdb->query("ALTER TABLE ".$wpdb->prefix."huge_itslider_sliders MODIFY `published` text");
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
//	  $wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_images SET  ordering = '".$rowimages->id."'  WHERE ID = ".$rowimages->id." ");
              $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."huge_itslider_images SET  ordering = '%s'  WHERE id = %d ", $rowimages->id,$rowimages->id));
	  }
			}
    }

		if($product2[6]['Field'] == 'sl_type')
			{
			echo '';
			}
			else
			{
			$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_images ADD  `sl_type` TEXT NOT NULL AFTER  `sl_url`");
			$wpdb->query("UPDATE ".$wpdb->prefix."huge_itslider_images SET sl_type = 'image' ");
			$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_images ADD  `link_target` TEXT NOT NULL AFTER  `sl_type`");
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
	$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_sliders ADD  `sl_position` TEXT NOT NULL AFTER  `param`");
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
	$productSliders = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_sliders", ARRAY_A);//get table fields
   $isUpdate1 = 0;
	foreach ($productSliders as $PSlider) {
        if ($PSlider['Field'] == 'sl_loading_icon') {
            $isUpdate1 = 1;
			break;
		}
	}
	if ($isUpdate1 == 0) {
            $wpdb->query("ALTER TABLE "  .$wpdb->prefix . "huge_itslider_sliders ADD `sl_loading_icon` text NOT NULL AFTER `published`");
            $wpdb->query("UPDATE " . $wpdb->prefix ."huge_itslider_sliders SET `sl_loading_icon` = 'off' ");
	}

	$product4 = $wpdb->get_results("DESCRIBE " . $wpdb->prefix . "huge_itslider_images", ARRAY_A);
	if($product4[8]['Field'] == 'sl_stitle'){
	}
	else
	{
	echo '';
		$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_images ADD  `sl_stitle` TEXT NOT NULL AFTER  `link_target`");
		$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_images ADD  `sl_sdesc` TEXT NOT NULL AFTER  `sl_stitle`");
		$wpdb->query("ALTER TABLE  ".$wpdb->prefix."huge_itslider_images ADD  `sl_postlink` TEXT NOT NULL AFTER  `sl_sdesc`");
	}	
$table_name = $wpdb->prefix . "huge_itslider_params";
$sql_update4 = <<<query2
INSERT INTO `$table_name` (`name`, `title`,`description`, `value`) VALUES
('loading_icon_type', 'Slider loading icon type', 'Slider loading icon type', '1');
query2;
        $query3="SELECT name FROM ".$table_name;
	$update_p3=$wpdb->get_results($query3);
	if(end($update_p3)->name=='slider_show_arrows'){
		$wpdb->query($sql_update4);
	}
}
register_activation_hook(__FILE__, 'huge_it_slider_activate');