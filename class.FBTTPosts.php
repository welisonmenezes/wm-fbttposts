<?php 
class FBTTPosts
{
	public static $post_types;

	/**
	 * load FBTTPosts
	 *
	 * @return void
	 */
	public static function fbttposts_load()
	{
		// activation of plugin
		register_activation_hook(plugin_dir_path( __FILE__ ).'wm-fbttposts.php', array('FBTTPosts', 'fbttposts_activation'));

		// dectivation of plugin
		register_deactivation_hook(plugin_dir_path( __FILE__ ).'wm-fbttposts.php', array('FBTTPosts', 'fbttposts_deactivation'));

		// add menu config
		add_action('admin_menu', array('FBTTPosts',  'fbttposts_add_menu'));

		// add metaboxes
		add_action( 'add_meta_boxes', array('FBTTPosts', 'fbttposts_add_metabox'));

    	// add styles
    	add_action('admin_enqueue_scripts', array('FBTTPosts', 'fbttposts_add_styles'));
		
    	// add action to posts
		//add_action( 'save_post', array('FBTTPosts', 'fbttposts_add_post'), 10, 3 );
		add_filter( 'wp_insert_post_data' , array('FBTTPosts', 'fbttposts_add_post') , '99', 2 );
		
		// save configurations
		add_action('init', array('FBTTPosts', 'fbttposts_save_config'),199);

		add_action('init', array('FBTTPosts', 'fbttposts_get_post_types'), 200);
	}

	function fbttposts_get_post_types(){
		self::$post_types = get_post_types(array('public'=>true), 'names', 'and');
	}

	/**
	 * add menus FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_add_menu() 
	{
		//add_options_page('FBTTPosts', 'FBTTPosts', 'manage_options', 'wm-fbttposts-config', array('FBTTPosts', 'fbttposts_view_config'));
		add_menu_page('FBTTPosts', 'FBTTPosts', 'manage_options', 'wm-fbttposts-config', array('FBTTPosts', 'fbttposts_view_config'), '', 6);
		
		add_submenu_page('wm-fbttposts-config', 'Connect', 'Connect', 'manage_options', 'wm-fbttposts-connect', array('FBTTPosts', 'fbttposts_view_connect'));
	}

	/**
	 * add metabox FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_add_metabox()
	{
		$cpt = get_option('wm-postTypes');
		if($cpt!=false)
		{	
			$pt = unserialize($cpt);
		}
		$screens = (count($pt)<=0) ? array('post', 'page') : $pt;
		foreach( $screens as $screen ) 
		{
			add_meta_box( 'FBTTPosts', 'FBTTPosts', array('FBTTPosts', 'fbttposts_view_metabox'), $screen, 'advanced','high', null);
		}
	}

	/**
	 * load css FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_add_styles()
	{
		wp_register_style( 'wm_fbttposts_styles', FBTTPOSTS_PLUGIN_URL . '/views/css/wm_fbttposts.css', false, '1.0.0' );
    	wp_enqueue_style( 'wm_fbttposts_styles' );
	}

	/**
	 * call in plugin activation
	 *
	 * @return void
	 */
	public static function fbttposts_activation()
	{
		update_option('wm-optWhere', 'default');
		update_option('wm-optContent', 'default');
		update_option('wm-optImage', 'default');

		$cpt = array('page', 'post');
		update_option('wm-postTypes', serialize($cpt));
	}

	/**
	 * call in plugin deactivation
	 *
	 * @return void
	 */
	public static function fbttposts_deactivation()
	{
		// facebook options
		delete_option( 'wm-fbAppId');
		delete_option( 'wm-fbAppSecret');

		// twitter options
		delete_option( 'wm-ttConsumerKey');
		delete_option( 'wm-ttConsumerSecret');
		delete_option( 'wm-ttToken');
		delete_option( 'wm-ttSecret');

		// general options
		delete_option('wm-optWhere');
		delete_option('wm-optContent');
		delete_option('wm-optImage');

		delete_option('wm-postTypes');
	}

	

	/**
	 * render html metabox FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_view_metabox()
	{
		FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_metabox.php');
	}

	/**
	 * render html config FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_view_config()
	{
		$data = array(
			'post_types' => self::$post_types
		);
		echo FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_config.php', false, $data);
	}

	/**
	 * render html connect FBTTPosts
	 *
	 * @return void
	 */
	public function fbttposts_view_connect()
	{
		FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_connect.php');
	}

	/**
	 * call funcitons to configurations of plugin
	 *
	 * @return void
	 */
	public function fbttposts_save_config()
	{
		if(	isset($_POST['action']) && 
			$_POST['action']=='wm-fbttposts-save-config' &&
			check_admin_referer('wm-fbttposts-save-config', 'wm_fbttposts_config') )
		{
			//var_dump($_POST);
			//*
			self::fbttposts_config_fb();
			self::fbttposts_config_tt();
			self::fbttposts_config_opt();
			//*/
		}
	}

	/**
	 * save configurations of facebook
	 *
	 * @return void
	 */
	protected function fbttposts_config_fb()
	{
		if(isset($_POST['wm-fbAppId']))
			update_option( 'wm-fbAppId', $_POST['wm-fbAppId'] );

		if(isset($_POST['wm-fbAppSecret']))
			update_option( 'wm-fbAppSecret', $_POST['wm-fbAppSecret'] );
	}

	/**
	 * save configurations of twitter
	 *
	 * @return void
	 */
	protected function fbttposts_config_tt()
	{
		if(isset($_POST['wm-ttConsumerKey']))
			update_option( 'wm-ttConsumerKey', $_POST['wm-ttConsumerKey'] );

		if(isset($_POST['wm-ttConsumerSecret']))
			update_option( 'wm-ttConsumerSecret', $_POST['wm-ttConsumerSecret'] );

		if(isset($_POST['wm-ttToken']))
			update_option( 'wm-ttToken', $_POST['wm-ttToken'] );

		if(isset($_POST['wm-ttSecret']))
			update_option( 'wm-ttSecret', $_POST['wm-ttSecret'] );
	}

	/**
	 * save general configurations
	 *
	 * @return void
	 */
	protected function fbttposts_config_opt()
	{
		if(isset($_POST['wm-optWhere']))
			update_option( 'wm-optWhere', $_POST['wm-optWhere'] );

		if(isset($_POST['wm-optContent']))
			update_option( 'wm-optContent', $_POST['wm-optContent'] );

		if(isset($_POST['wm-optImage']))
			update_option( 'wm-optImage', $_POST['wm-optImage'] );

		if(isset($_POST['wm-postTypes']))
		{
			update_option('wm-postTypes', serialize($_POST['wm-postTypes']));
		}
	}




	public static function fbttposts_add_post($data , $postarr)
	{
		if(isset($postarr['wm-fbttposts-status']))
		{
			update_post_meta($postarr['ID'], 'wm-fbttposts-status', $postarr['wm-fbttposts-status']);
		}
		
		if(isset($postarr['wm-fbttposts-image']))
		{
			update_post_meta($postarr['ID'], 'wm-fbttposts-image', $postarr['wm-fbttposts-image']);
		}
		
		if(isset($postarr['wm-fbttposts-json']))
		{
			update_post_meta($postarr['ID'], 'wm-fbttposts-json', $postarr['wm-fbttposts-json']);
		}
	}

}