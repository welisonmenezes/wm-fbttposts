<?php 
/**
 * facebook configurations
 */
$fbConfig = array(
	'app_id' => get_option('wm-fbAppId'),
	'app_secret' => get_option('wm-fbAppSecret'),
	'app_callback' => FBTTPOSTS_FULL_URL
);

/*
 *	twitter configurations
 */
$ttConfig = array(
	'consumer_key'    => get_option('wm-ttConsumerKey'),
	'consumer_secret' => get_option('wm-ttConsumerSecret'),
	'token'           => get_option('wm-ttToken'),
	'secret'          => get_option('wm-ttSecret'),
	'user_agent' => 'TwitterOAuth v0.2.0-beta2'
);

/*
 *	facebook api classes
 */
require __DIR__ . '/libs/Facebook/autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSDKException;

/*
 *	twitter api classes
 */
require __DIR__ . '/libs/Twitter/tmhOAuth.php';

class FBTTPostsShare
{	
	public static $fb_session;
	public static $fb_login;
	public static $fb_login2;

	public static $arr_return;

	public static $tt_instance;

	public function __construct($fbConfig, $ttConfig)
	{
		// load fbttposts_share function
		self::fbttposts_load();

		self::$arr_return = array();

		if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='facebook')
		{
			// init facebook classes
			if(get_option('wm-fbAppId')!=false && get_option('wm-fbAppSecret')!=false)
			{
				self::fbttposts_init_fb($fbConfig);
			}
		}
		
		if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='twitter')
		{
			// init twitter classes
			if(get_option('wm-ttConsumerSecret')!=false && get_option('wm-ttConsumerKey')!=false)
			{
				self::fbttposts_init_tt($ttConfig);

				// get credencial twitter
	  			self::fbttposts_get_credential_tt();
			}
		}
	}

	/**
	 * load FBTTPostsShare
	 *
	 * @return void
	 */
	public function fbttposts_load()
	{
		//  load postshare
		add_action('init', array('FBTTPostsShare', 'fbttposts_share'));
		FBTTPostsShare::fbttposts_get_file_by_guid();
				
	}

	/**
	 * wait ajax request to get id file by url
	 *
	 * @return void
	 */
	public function fbttposts_get_file_by_guid()
	{
		if (isset($_POST['action']) && $_POST['action'] == 'wm-fbttposts-send-guid')
		{
			if(isset($_POST['guid']) && !empty($_POST['guid']))
			{
				global $wpdb;
				$url = $_POST['guid'];
				$table_name = $wpdb->prefix.'_posts';
				$thepost = $wpdb->get_results( "SELECT ID FROM wp_posts WHERE guid = '".$url."'", OBJECT );
				if(!empty($thepost))
				{
					$thumb = FBTTPOstsUtil::fbttposts_get_info_file($thepost[0]->ID);
					echo json_encode($thumb);
				}
			}
			die();
		}
	}

	/**
	 * init dependencies twitter
	 *
	 * @return void
	 */
	public function fbttposts_init_tt($ttConfig)
	{
		self::$tt_instance = new tmhOAuth($ttConfig);
	}

	/**
	 * wait ajax request to get credentials from twitter
	 *
	 * @return void
	 */
	public function fbttposts_get_credential_tt()
	{
		if (isset($_GET['oauth_token']) && isset($_GET['oauth_verifier'])) 
		{
			self::$tt_instance->config['token'] = $_SESSION['oauth']['oauth_token'];
			self::$tt_instance->config['secret'] = $_SESSION['oauth']['oauth_token_secret'];
			self::$tt_instance->reconfigure(self::$tt_instance->config);

			$code_3 = self::$tt_instance->user_request(array(
				'method' => 'POST',
				'url' => self::$tt_instance->url('oauth/access_token', ''),
				'params' => array(
					'oauth_verifier' => trim($_GET['oauth_verifier']),
				)
			));
			$response = self::$tt_instance->response['response'];

			$r = explode('&', $response);
			if(isset($r[0]) && !empty($r[0]))
			{
				$t_r = explode('=', $r[0]);
				if(isset($t_r[1]) && !empty($t_r[1]))
					$oauth_token = $t_r[1];
			}
			if(isset($r[1]) && !empty($r[1]))
			{
				$t_r = explode('=', $r[1]);
				if(isset($t_r[1]) && !empty($t_r[1]))
					$oauth_token_secret = $t_r[1];
			}

			update_option( 'wm-ttToken', $oauth_token );
			update_option( 'wm-ttSecret', $oauth_token_secret );

			unset($_SESSION['oauth']);

			header('Location: '.FBTTPOSTS_FULL_URL);
			die;
		}
	}

	/**
	 * get link login whit twitter
	 *
	 * @return void
	 */
	public function fbttposts_tt_login($target=false)
	{
		unset($_SESSION['oauth']);
		$code = self::$tt_instance->apponly_request(array(
			'without_bearer' => true,
			'method' => 'POST',
			'url' => self::$tt_instance->url('oauth/request_token', ''),
			'params' => array(
				'oauth_callback' => FBTTPOSTS_FULL_URL,
			),
		));

		if($code != 200){return;}

		// store the params into the session so they are there when we come back after the redirect
		$_SESSION['oauth'] = self::$tt_instance->extract_params(self::$tt_instance->response['response']);

		// check the callback has been confirmed
		if ($_SESSION['oauth']['oauth_callback_confirmed'] === 'true')
		{
			$url = self::$tt_instance->url('oauth/authorize', '') . "?oauth_token={$_SESSION['oauth']['oauth_token']}";
			$data['url'] = $url;
			$data['target'] = $target;
			return FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_tt_login.php', false, $data);
		}
	}

	/**
	 * verify if it is connected with twitter
	 *
	 * @return boolean
	 */
	public function fbttposts_tt_has_credentials()
	{
		self::$tt_instance->config['token'] = get_option( 'wm-ttToken');
  		self::$tt_instance->config['secret'] = get_option( 'wm-ttSecret');
  		if(!self::$tt_instance->config['token'] || !self::$tt_instance->config['secret'] ) 
  		{
  			die();
  		}
  		self::$tt_instance->reconfigure(self::$tt_instance->config);
		$code = self::$tt_instance->user_request(array(
		  'url' => self::$tt_instance->url('1.1/account/verify_credentials')
		));
		if($code === 200) return true;
		return false;
	}

	/**
	 * publish post in twitter
	 *
	 * @return void
	 */
	public function fbttposts_tt_publish($data)
	{	
		$img 	= $data['source'];
		$mime 	= $data['mime'];
		$file 	= $data['file'];
		$params = array(
			'media[]' => "@{$img};type={$mime};filename={$file}",
			'status'  => $data['status']
		);
		$code2 = self::$tt_instance->user_request(array(
			'method' => 'POST',
			'url' => self::$tt_instance->url("1.1/statuses/update_with_media"),
			'params' => $params,
			'multipart' => true
		));
	}

	/**
	 * init dependencies facebook
	 *
	 * @return void
	 */
	public function fbttposts_init_fb($fbConfig)
	{
		FacebookSession::setDefaultApplication($fbConfig['app_id'], $fbConfig['app_secret']);
		$helper = new FacebookRedirectLoginHelper($fbConfig['app_callback']);

		if(isset($_SESSION['wm_fbttposts']['token']))
		{
			self::$fb_session = new FacebookSession($_SESSION['wm_fbttposts']['token']);
		    try
		    {
		        self::$fb_session->Validate($fbConfig['app_id'] ,$fbConfig['app_secret']);
		    }
		    catch( FacebookSDKException $ex)
		    {
		        self::$fb_session = null;
		        unset($_SESSION['wm_fbttposts']['token']);
		    }
		}
		else
		{
			try 
			{
			    self::$fb_session = $helper->getSessionFromRedirect();
			    if (self::$fb_session) 
				{	
					$accessToken = self::$fb_session->getToken();
					$_SESSION['wm_fbttposts']['token'] = $accessToken;
				} 
				else 
				{	
				    self::$fb_login = self::fbttposts_view_fb_login($helper);
				    self::$fb_login2 = self::fbttposts_view_fb_login($helper, true);
				}
			} 
			catch(FacebookSDKException $e) 
			{
			    self::$fb_session = null;
			    die('-1');
			}
		}
	}

	/**
	 * html link login facebook
	 *
	 * @return void
	 */
	public function fbttposts_view_fb_login($data, $two = false)
	{
		if(!$two)
			return FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_fb_login.php', false, $data);
		else
			return FBTTPostsUtil::fbttposts_render_view(FBTTPOSTS_PLUGIN_DIR.'/views/view_fb_login2.php', false, $data);
	}

	/**
	 * publish post in facebook
	 *
	 * @return void
	 */
	public function fbttposts_fb_publish($s, $data)
	{
		$privacy = array(
	        'value' => 'SELF'
	    );
		$response = (new FacebookRequest(
	        $s, 'POST', '/me/feed', array(
	            'message'  	=> $data['status'],
	            'link'		=> $data['link'],
	        	'source'  	=> $data['image'],
	        	'privacy'	=>json_encode($privacy)
	        )
	    ))->execute()->getGraphObject();
	}

	/**
	 * publish post
	 *
	 * @return void
	 */
	public function fbttposts_share()
	{
		if(isset($_POST['action']) && $_POST['action']=='wm-fbttposts-share-post')
		{
			$data = array(
				'post_id'=> $_POST['post_id'],
				'status' => $_POST['post_status'],
				'image'  => $_POST['post_image'],
				'source' => rawurldecode($_POST['post_source']),
				'mime'   => $_POST['post_mime'],
				'file'   => $_POST['post_file'],
				'link'   => $_POST['post_link']
 			);

 			$fb_can = false;
 			$tt_can = false;

			check_ajax_referer('wm-fbttposts-share-post', 'security');

			if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='twitter')
			{
				// twitter 
				if(self::fbttposts_tt_has_credentials())
				{	
					$tt_can = true;
				}
				else
				{
					$tt_can = false;
				}
			}
			else
			{
				$tt_can = true;
			}

			if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='facebook')
			{
				// facebook
				if(self::$fb_session)
				{	
					$fb_can = true;
				}
				else{
					$fb_can = false;
				}
			}
			else
			{
				$fb_can = true;
			}

			if($fb_can===true && $tt_can===true)
			{
				if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='twitter')
				{
					// facebook publica aqui
					self::fbttposts_tt_publish($data);
				}

				if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='facebook')
				{
					// twitter publica aqui
					self::fbttposts_fb_publish(self::$fb_session, $data);
				}

				update_post_meta($data['post_id'], 'wm-fbttposts-publish', 'published');

			}else{
				if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='twitter')
				{
					if($tt_can===false)
						echo self::fbttposts_tt_login();
				}

				if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='facebook')
				{
					if($fb_can===false)
						echo self::$fb_login;
				}

				die();
			}

			self::$arr_return['tt_can'] = $tt_can;
			self::$arr_return['fb_can'] = $fb_can;

			echo json_encode(self::$arr_return);
			die();
		}
		
	}
}
?>