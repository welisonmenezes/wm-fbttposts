<?php 
	$url = $data->getLoginUrl(
		array(
			'manage_pages',
			'publish_actions',
			'public_profile',
			'read_stream',
			'friends_likes',
			'offline_access'
		)
	);
?>
<p style="color:red;">
	<?php echo __('Você não está conectado no Facebook. Clique no botão abaixo para se conectar.'); ?>
</p>
<a href="<?php echo $url; ?>" target="_blank" class="button button-default" title="<?php echo __('Login com o Facebook'); ?>">
	<?php echo __('Login com o Facebook'); ?>
</a>