<?php
/*
* Plugin Name: Woobizz Templates
 * Plugin URI: https://woobizz.com
 * Description: Woobizz customs templates templates
 * Author: Woobizz
 * Author URI: https://woobizz.com
 * Version: 1.0.0
 * Text Domain: woobizz-templates
 * Domain Path: /lang/
*/
class WoobizzTemplates {
	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;
	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;
	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new WoobizzTemplates();
		} 
		return self::$instance;
	} 
	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
	private function __construct() {
		$this->templates = array();
		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
			// 4.6 and older
			add_filter(
				'page_attributes_dropdown_templates_args',
				array( $this, 'register_project_templates' )
			);
		} else {
			// Add a filter to the wp 4.7 version attributes metabox
			add_filter(
				'theme_page_templates', array( $this, 'add_new_template' )
			);
		}
		// Add a filter to the save post to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'register_project_templates' ) 
		);
		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);
		// Add your templates to this array.
		$this->templates = array(
			'/templates/woobizz-full-width-1.php' => '01 Woobizz Full-Width: +Headers +Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-2.php' => '02 Woobizz Full-Width: -Headers -Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-3.php' => '03 Woobizz Full-Width: +Headers +Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-4.php' => '04 Woobizz Full-Width: -Headers +Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-5.php' => '05 Woobizz Full-Width: +Headers +Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-6.php' => '06 Woobizz Full-Width: +Headers -Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-7.php' => '07 Woobizz Full-Width: +Headers +Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-8.php' => '08 Woobizz Full-Width: -Headers -Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-9.php' => '09 Woobizz Full-Width: -Headers +Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-10.php' => '10 Woobizz Full-Width: -Headers +Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-11.php' => '11 Woobizz Full-Width: +Headers -Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-12.php' => '12 Woobizz Full-Width: +Headers -Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-13.php' => '13 Woobizz Full-Width: +Headers -Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-14.php' => '14 Woobizz Full-Width: -Headers +Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-15.php' => '15 Woobizz Full-Width: -Headers -Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-16.php' => '16 Woobizz Full-Width: -Headers -Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-17.php' => '17 Woobizz Standard: +Headers +Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-18.php' => '18 Woobizz Standard: -Headers -Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-19.php' => '19 Woobizz Standard: +Headers +Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-20.php' => '20 Woobizz Standard: -Headers +Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-21.php' => '21 Woobizz Standard: +Headers +Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-22.php' => '22 Woobizz Standard: +Headers -Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-23.php' => '23 Woobizz Standard: +Headers +Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-24.php' => '24 Woobizz Standard: -Headers -Breadcrumb +Title +Footers',
			'/templates/woobizz-full-width-25.php' => '25 Woobizz Standard: -Headers +Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-26.php' => '26 Woobizz Standard: -Headers +Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-27.php' => '27 Woobizz Standard: +Headers -Breadcrumb -Title +Footers',
			'/templates/woobizz-full-width-28.php' => '28 Woobizz Standard: +Headers -Breadcrumb +Title -Footers',
			'/templates/woobizz-full-width-29.php' => '29 Woobizz Standard: +Headers -Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-30.php' => '30 Woobizz Standard: -Headers +Breadcrumb -Title -Footers',
			'/templates/woobizz-full-width-31.php' => '31 Woobizz Standard: -Headers -Breadcrumb +Title -Footers',	
			'/templates/woobizz-full-width-32.php' => '32 Woobizz Standard: -Headers -Breadcrumb -Title +Footers',		
		);
	} 
	/**
	 * Adds our template to the page dropdown for v4.7+
	 *
	 */
	public function add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}
	/**
	 * Adds our template to the templates cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 */
	public function register_project_templates( $atts ) {
		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 
		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');
		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );
		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
		return $atts;
	} 
	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_project_template( $template ) {
		// Get global post
		global $post;
		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}
		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		} 
		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);
		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} 
		// Return template
		return $template;
	}
} 
add_action( 'plugins_loaded', array( 'WoobizzTemplates', 'get_instance' ) );
//WOOBIZZ FULL WIDTH 1 TEMPLATE 
	function woobizz_full_width_1_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_1_template_css' );
	}
	//WOOBIZZ FULL WIDTH 2 TEMPLATE 
	function woobizz_full_width_2_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_2_template_css' );
	}
	//WOOBIZZ FULL WIDTH 3 TEMPLATE 
	function woobizz_full_width_3_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_3_template_css' );
	}
	//WOOBIZZ FULL WIDTH 4 TEMPLATE 
	function woobizz_full_width_4_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_4_template_css' );
	}
	//WOOBIZZ FULL WIDTH 5 TEMPLATE 
	function woobizz_full_width_5_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_5_template_css' );
	}
	//WOOBIZZ FULL WIDTH 6 TEMPLATE 
	function woobizz_full_width_6_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_6_template_css' );
	}
	//WOOBIZZ FULL WIDTH 7 TEMPLATE 
	function woobizz_full_width_7_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_7_template_css' );
	}
	//WOOBIZZ FULL WIDTH 8 TEMPLATE 
	function woobizz_full_width_8_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_8_template_css' );
	}
	//WOOBIZZ FULL WIDTH 9 TEMPLATE 
	function woobizz_full_width_9_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}			
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_9_template_css' );
	}
	//WOOBIZZ FULL WIDTH 8 TEMPLATE 
	function woobizz_full_width_10_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}			
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_10_template_css' );
	}
	//WOOBIZZ FULL WIDTH 11 TEMPLATE 
	function woobizz_full_width_11_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_11_template_css' );
	}
	//WOOBIZZ FULL WIDTH 12 TEMPLATE 
	function woobizz_full_width_12_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_12_template_css' );
	}
	//WOOBIZZ FULL WIDTH 13 TEMPLATE 
	function woobizz_full_width_13_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_13_template_css' );
	}
	//WOOBIZZ FULL WIDTH 14 TEMPLATE 
	function woobizz_full_width_14_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_14_template_css' );
	}
	//WOOBIZZ FULL WIDTH 15 TEMPLATE 
	function woobizz_full_width_15_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_15_template_css' );
	}
	//WOOBIZZ FULL WIDTH 16 TEMPLATE 
	function woobizz_full_width_16_template_css(){
		echo"
		<style>
			div#content{padding: 0% 0%!important;}
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.right-sidebar .content-area{width: 100%!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_full_width_16_template_css' );
	}
		//WOOBIZZ STANDARD 17 TEMPLATE 
	function woobizz_standard_17_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_1_template_css' );
	}
	//WOOBIZZ STANDARD 18 TEMPLATE 
	function woobizz_standard_18_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_2_template_css' );
	}
	//WOOBIZZ STANDARD 19 TEMPLATE 
	function woobizz_standard_19_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_3_template_css' );
	}
	//WOOBIZZ STANDARD 20 TEMPLATE 
	function woobizz_standard_20_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_4_template_css' );
	}
	//WOOBIZZ STANDARD 21 TEMPLATE 
	function woobizz_standard_21_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_5_template_css' );
	}
	//WOOBIZZ STANDARD 22 TEMPLATE 
	function woobizz_standard_22_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_6_template_css' );
	}
	//WOOBIZZ STANDARD 23 TEMPLATE 
	function woobizz_standard_23_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_7_template_css' );
	}
	//WOOBIZZ STANDARD 24 TEMPLATE 
	function woobizz_standard_24_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_8_template_css' );
	}
	//WOOBIZZ STANDARD 25 TEMPLATE 
	function woobizz_standard_25_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}			
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_9_template_css' );
	}
	//WOOBIZZ STANDARD 26 TEMPLATE 
	function woobizz_standard_26_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}			
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_10_template_css' );
	}
	//WOOBIZZ STANDARD 27 TEMPLATE 
	function woobizz_standard_27_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_11_template_css' );
	}
	//WOOBIZZ STANDARD 28 TEMPLATE 
	function woobizz_standard_28_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_12_template_css' );
	}
	//WOOBIZZ STANDARD 29 TEMPLATE 
	function woobizz_standard_29_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_13_template_css' );
	}
	//WOOBIZZ STANDARD 30 TEMPLATE 
	function woobizz_standard_30_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_14_template_css' );
	}
	//WOOBIZZ STANDARD 31 TEMPLATE 
	function woobizz_standard_31_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			.wb-beforefooter-sidebar-div{display:none!important;}
			.wb-customfooter-sidebar-div{display:none!important;}
			.site-footer{display:none!important;}
			.wb-afterfooter-sidebar-div{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_15_template_css' );
	}
	//WOOBIZZ STANDARD 32 TEMPLATE 
	function woobizz_standard_32_template_css(){
		echo"
		<style>
			header.entry-header{margin:20px!important;}
			.hentry{margin:0!important;}
			.site-main{margin-bottom:0!important;}
			.wb-beforeheader-sidebar-div{display:none!important;}
			header#masthead{display:none!important;}
			.wb-customheader-sidebar-div{display:none!important;}
			.wb-afterheader-sidebar-div{display:none!important;}
			.wb-breadcrumb-sidebar-div{display:none!important;}
			header.entry-header{display:none!important;}
		</style>";
		add_action( 'init', 'woobizz_standard_16_template_css' );
	}