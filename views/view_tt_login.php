<p style="color:red;">
	<?php echo __('Você não está conectado no Twitter. Clique no botão abaixo para se conectar.'); ?>
</p>
<?php 
$target = "_blank";
if($data['target']===true)
	$target = "";
?>
<a href="<?php echo $data['url']; ?>" target="<?php echo $target; ?>" class="button button-default" title="<?php echo __('Login com o Twitter'); ?>" target="_blank">
	<?php echo __('Login com o Twitter'); ?>
</a>