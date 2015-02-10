<div class="wm_fbttposts_wrap">
	<h2><?php echo __('Verifique a conexão com as redes sociais configuradas.'); ?></h2>

	<?php 
	$fb_can = false;
	 $tt_can = false;
	if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='twitter')
	{
		if(!FBTTPostsShare::fbttposts_tt_has_credentials())
		{
			echo FBTTPostsShare::fbttposts_tt_login(true);
			$fb_can = false;
		}
		else
		{
			$fb_can = true;
		}
	}
	else
	{
		$fb_can = true;
	}

	if(get_option('wm-optWhere')=='default' || get_option('wm-optWhere')=='facebook')
	{
		if(!FBTTPostsShare::$fb_session)
		{
			echo FBTTPostsShare::$fb_login2;
			$tt_can = false;
		}
		else
		{
			$tt_can = true;
		}
	}
	else
	{
		$tt_can = true;
	}

	if($fb_can===true && $tt_can===true)
	{
		echo '<div class="wm-alert">'.__('Você está conectado!').'</div>';
	}
	?>
</div>