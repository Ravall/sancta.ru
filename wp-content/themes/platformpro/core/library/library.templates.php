<?php
/**
 * This file contains a library of common templates accessed by functions
 *
 * @package PageLines Core
 *
 **/

// ======================================
// = Sidebar Setup & Template Functions =
// ======================================

/**
 * Sidebar - Call & Markup
 *
 */

function pagelines_draw_sidebar($id, $name, $default = null){?>
	<ul id="<?php echo 'list_'.$id; ?>" class="sidebar_widgets fix">
		<?php if (!dynamic_sidebar($name)){ pagelines_default_widget( $id, $name, $default); } ?>
	</ul>
<?php }

/**
 * Sidebar - Default Widget
 *
 */
function pagelines_default_widget($id, $name, $default){
	if(isset($default) && !pagelines('sidebar_no_default')):
	
		get_template_part( $default ); 
		
	elseif(!pagelines('sidebar_no_default')):
	?>	

	<li class="widget-default no_<?php echo $id;?>">
			<h3 class="widget-title">Add Widgets (<?php echo $name;?>)</h3>
			<p>This is your <?php echo $name;?>. Edit this content that appears here in the <a href="<?php echo admin_url('widgets.php');?>">widgets panel</a> by adding or removing widgets in the <?php echo $name;?> area.
			</p>
	</li>

<?php endif;
	}

/**
 * Sidebar - Standard Sidebar Setup
 *
 */
function pagelines_standard_sidebar($name, $description){
	return array(
		'name'=> $name,
		'description' => $description,
	    'before_widget' => '<li id="%1$s" class="%2$s widget fix"><div class="widget-pad">',
	    'after_widget' => '</div></li>',
	    'before_title' => '<h3 class="widget-title">',
	    'after_title' => '</h3>'
	);
}


/**
 * Javascript Confirmation
 *
 * @param string $name Function name, to be used in the input
 * @param string $text The text of the confirmation
 */
function pl_action_confirm($name, $text){ ?>
	<script language="jscript" type="text/javascript">
		function <?php echo $name;?>(){	
			var a = confirm ("<?php echo esc_js( $text );?>");
			if(a) {
				jQuery("#input-full-submit").val(1);
				return true;
			} else return false;
		}
	</script>
<?php }


// Title and External Script Integration
function pagelines_head_common(){
	?>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<?php 
	/*
		Title Metatag
	*/
	echo "\n<title>";
	if( pagelines_bbpress_forum() ){
		bb_title();
	}elseif(pagelines_is_buddypress_page()){
		bp_page_title();
	}else{
		if(is_front_page()) { echo get_bloginfo('name'); } else { wp_title(''); }
	}
	echo "</title>\n";
	
	if(!VDEV) { echo "<!-- Platform WordPress Framework By PageLines - www.PageLines.com -->\n\n";}
	/*
		Meta Images
	*/
	if(pagelines_option('pagelines_favicon')){
		echo '<link rel="shortcut icon" href="'.pagelines_option('pagelines_favicon').'" type="image/x-icon" />';
	}
	if(pagelines_option('pagelines_touchicon')){
		echo '<link rel="apple-touch-icon" href="'.pagelines_option('pagelines_touchicon').'" />';
	}
	if(pagelines_option('gfonts')): ?>
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=<?php print_pagelines_option('gfonts_families', 'molengo');?>">
	<?php endif;
	echo ( !apply_filters( 'pagelines_xfn', '' ) )  ? "\r\n<link rel=\"profile\" href=\"http://gmpg.org/xfn/11\" />\r\n" : '';
	if( pagelines_bbpress_forum() ){ // Load bbPress headers 	
			bb_feed_head();
			bb_head(); 
			echo '<link rel="stylesheet" id="pagelines-bbpress-css" href="';
			bb_stylesheet_uri();
			echo '" type="text/css" />';
			
			// Enqueued Stuff doesn't show in bbPress
			// So we have to load the CSS manually....
			if(VPRO){
				echo '<link rel="stylesheet" id="pagelines-pro-css" href="';
				echo PAGELINES_PRO_ROOT.'/pro.css?ver='.CORE_VERSION;
				echo '" type="text/css" />';
			}
			
			echo '<link rel="stylesheet"id="pagelines-platform-css"  href="';
			bloginfo('stylesheet_url');
			echo '" type="text/css" />';
			
		
	}

	// Get Pro Styles
	if(VPRO){
		wp_register_style('pagelines-pro', PAGELINES_PRO_ROOT.'/pro.css', array(), CORE_VERSION, 'all');
	    wp_enqueue_style( 'pagelines-pro');
	}
	
	// Get Main Styles
	wp_register_style('pagelines-stylesheet', get_bloginfo('stylesheet_url'), array(), CORE_VERSION, 'all');
    wp_enqueue_style( 'pagelines-stylesheet');

	// RTL Language Support
	if(is_rtl()) {
		echo '<link rel="stylesheet" id="pagelines-rtl" href="';
		echo THEME_ROOT.'/rtl.css?ver='.CORE_VERSION;
		echo '" type="text/css" />';
	}
	
	// Queue Common Javascript Libraries
	wp_enqueue_script("jquery"); 
	
	// TODO - add this to the comment section code instead
	if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' ); // This makes the comment box appear where the ‘reply to this comment’ link is
}
	
function do_dynamic_css(){
	// Get Dynamic Styles
		// If multisite is used, then dynamic styles need to be inline.
		// TODO is there a better solution?

	if(is_multisite()){
		get_dynamic_css();
	} if(file_exists(PAGELINES_DCSS)){
	
		echo '<link rel="stylesheet" id="pagelines-dynamic-css" href="';

		echo PAGELINES_DCSS_URI . '?ver=' . CORE_VERSION;

		echo '" type="text/css" media="all" />'."\n";
		
		
	} else {
		// Deprecated location, remove by 1.5.0
		echo '<link rel="stylesheet" id="pagelines-dynamic-css" href="';
		echo CORE_CSS.'/dynamic.css?ver='.CORE_VERSION;
		echo '" type="text/css" media="all" />'."\n";
	}
}	
	
// Fix IE issues to the extent possible...
function pagelines_fix_ie($imagestofix = ''){?>
<?php if(pagelines('google_ie')):?>
<!--[if lt IE 8]> <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE8.js"></script> <![endif]-->
<?php endif;?>
<!--[if IE 6]>
<script src="<?php echo CORE_JS . '/ie.belatedpng.js';?>"></script> 
<script>DD_belatedPNG.fix('<?php echo $imagestofix;?>');</script>
<![endif]-->
<?php 

/*
	IE File Setting up with conditionals
	TODO Why doesnt WP allow you to conditionally enqueue scripts?
*/

// If IE6 add the Internet Explorer 6 specific stylesheet
	global $wp_styles;
	wp_enqueue_style('ie6-style', THEME_CSS  . '/ie6.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie6-style', 'conditional', 'lte IE 6' );
	
	wp_enqueue_style('ie7-style', THEME_CSS  . '/ie7.css', array(), CORE_VERSION);
	$wp_styles->add_data( 'ie7-style', 'conditional', 'IE 7' );
	
} 

function pagelines_font_replacement( $default_font = ''){
	
	if(pagelines_option('typekit_script')){
		echo pagelines_option('typekit_script');
	}
	
	if(pagelines_option('fontreplacement')){
		global $cufon_font_path;
		
		if(pagelines_option('font_file')) $cufon_font_path = pagelines_option('font_file');
		elseif($default_font) $cufon_font_path = THEME_JS.'/'.$default_font;
		else $cufon_font_path = null;
		
		// ===============================
		// = Hook JS Libraries to Footer =
		// ===============================
		add_action('wp_footer', 'font_replacement_scripts');
		function font_replacement_scripts(){
			
			global $cufon_font_path;

			wp_register_script('cufon', CORE_JS.'/type.cufon.js', 'jquery', '1.09', true);
			wp_print_scripts('cufon');
			
			if(isset($cufon_font_path)){
				wp_register_script('cufon_font', $cufon_font_path, 'cufon');
				wp_print_scripts('cufon_font');
			}
		
		}
		
		add_action('wp_head', 'cufon_inline_script');
		function cufon_inline_script(){
			?><script type="text/javascript"><?php 
			if(pagelines('replace_font')): 
				?>jQuery(document).ready(function () {
					Cufon.replace('<?php echo pagelines("replace_font"); ?>', {hover: true});
				});<?php 
			endif;
			?></script><?php
		 }
 	}
}


/**
 * 
 *  Fallback for navigation, if it isn't set up
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.1.0
 *
 */
function pagelines_nav_fallback() {
	global $post; ?>
	
	<ul id="menu-nav" class="main-nav<?php echo pagelines_nav_classes();?>">
		<?php wp_list_pages( 'title_li=&sort_column=menu_order&depth=3'); ?>
	</ul><?php
}

/**
 * 
 *  Returns child pages for subnav, setup in hierarchy
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.1.0
 *
 */
function pagelines_page_subnav(){ 
	global $post; 
	if(!is_404() && isset($post) && is_object($post) && !pagelines_option('hide_sub_header') && ($post->post_parent || wp_list_pages("title_li=&child_of=".$post->ID."&echo=0"))):?>
	<ul>
		<?php 
			if(count($post->ancestors)>=2){
				$reverse_ancestors = array_reverse($post->ancestors);
				$children = wp_list_pages("title_li=&depth=1&child_of=".$reverse_ancestors[0]."&echo=0&sort_column=menu_order");	
			}elseif($post->post_parent){ $children = wp_list_pages("title_li=&depth=1&child_of=".$post->post_parent."&echo=0&sort_column=menu_order");
			}else{	$children = wp_list_pages("title_li=&depth=1&child_of=".$post->ID."&echo=0&sort_column=menu_order");}

			if ($children) { echo $children;}
		?>
	</ul>
	<?php endif;
}

/**
 * 
 *  The main site logo template
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.1.0
 *
 */
function pagelines_main_logo(){ 
	if(pagelines_option('pagelines_custom_logo')){
		
		$site_logo = sprintf( '<a class="mainlogo-link" href="%s" title="%s"><img class="mainlogo-img" src="%s" alt="%s" /></a>', home_url(), get_bloginfo('name'), esc_url(pagelines_option('pagelines_custom_logo')), get_bloginfo('name'));
		
		echo apply_filters('pagelines_site_logo', $site_logo);
		
	} else {
		
		$site_title = sprintf( '<div class="title-container"><a class="home site-title" href="%s" title="%s">%s</a><h6 class="site-description subhead">%s</h6></div>', esc_url(home_url()), __('Home','pagelines'), get_bloginfo('name'), get_bloginfo('description'));
		
		echo apply_filters('pagelines_site_title', $site_title);
		
	}
		
}

/**
 * Adds the metabar or byline under the post title
 *
 * @since 4.1.0
 */
add_filter('pagelines_post_metabar', 'do_shortcode', 20);
function pagelines_get_post_metabar( $format = '' ) {
	
	$metabar = '';
	
	if ( is_page() )
		return; // don't do post-info on pages
	
	if( $format == 'clip'){
		
		$metabar .= sprintf( '<span class="sword">%s</span> [post_date] ', __('On','pagelines') );
		$metabar .= sprintf( '<span class="sword">%s</span> [post_author_posts_link] ', __('By','pagelines') );
			
	} else {
		
		if(pagelines_option('byline_author')){
			$metabar .= sprintf( '<span class="sword">%s</span> [post_author_posts_link] ', __('By','pagelines') );
		}

		if(pagelines_option('byline_date')){
			$metabar .= sprintf( '<span class="sword">%s</span> [post_date] ', __('On','pagelines') );
		}

		if(pagelines_option('byline_comments')){
			$metabar .= '&middot; [post_comments] ';
		}

		if(pagelines_option('byline_categories')){
			$metabar .= sprintf( '&middot; <span class="sword">%s</span> [post_categories]', __('In','pagelines') );
		}
		
	}
	
	$metabar .= ' [post_edit]';
	
	printf( '<div class="metabar"><em>%s</em></div>', apply_filters('pagelines_post_metabar', $metabar) );
	
}

/**
 * 
 *  Gets the Post Title for Blog Posts
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 4.1.0
 *
 */
function pagelines_get_post_title( $format = '' ){ 
	
	if ( is_singular() ) {
		$title = sprintf( '<h1 class="entry-title">%s</h1>', apply_filters( 'pagelines_post_title_text', get_the_title() ) );
	}
	
	elseif( $format == 'clip'){
		$title = sprintf( '<h4 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h4>', get_permalink(), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );
	}
	
	else {
		$title = sprintf( '<h2 class="entry-title"><a href="%s" title="%s" rel="bookmark">%s</a></h2>', get_permalink(), the_title_attribute('echo=0'), apply_filters( 'pagelines_post_title_text', get_the_title() ) );
	}
	
	echo apply_filters('pagelines_post_title_output', $title) . "\n";
	
}

/**
 * 
 *  Gets the continue reading link after excerpts
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.0
 *
 */
function get_continue_reading_link($post_id){

	$text = sprintf('%s <span class="right_arrow">%s</span>', load_pagelines_option('continue_reading_text', __('Continue Reading', 'pagelines')), __('&rarr;', 'pagelines'));

	$thetext = apply_filters('continue_reading_link_text', $text);

	$link = sprintf('<a class="continue_reading_link" href="%s" title="%s %s">%s</a>', get_permalink(), __("View", 'pagelines'), the_title_attribute(array('echo'=> 0)), $thetext );
	
	return apply_filters('continue_reading_link', $link);
}
/**
 * 
 *  Returns nav menu classes
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.1.0
 *
 */
function pagelines_nav_classes(){ 
	
	$additional_menu_classes = '';
	if(pagelines_option('hidesearch')){ $additional_menu_classes .= ' nosearch';}
	if(pagelines_option('enable_drop_down')){ $additional_menu_classes .= ' sf-menu';}
	
	return $additional_menu_classes;
}

/**
 * 
 *  Loads Special PageLines CSS Files, Optimized
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.2.0
 *
 */
function pagelines_draw_css( $css_url ){ 
	echo '<link href="'.$css_url.'" rel="stylesheet"/>'."\n";
}

/**
 * 
 *  Adds PageLines to Admin Bar
 *
 *  @package PageLines
 *  @subpackage Functions Library
 *  @since 1.3.0
 *
 */
function pagelines_settings_menu_link(  ){ 
	global $wp_admin_bar;

	if ( !current_user_can('edit_theme_options') )
		return;

	$wp_admin_bar->add_menu( array( 'id' => 'pagelines_settings_adminbar', 'title' => __("PageLines Settings", 'pagelines'), 'href' => admin_url( 'admin.php?page=pagelines' ) ) );
}



