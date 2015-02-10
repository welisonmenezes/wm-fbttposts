<?php
$pt = get_option('wm-postTypes');
$u_pt = false;
if(!empty($pt) && $pt!=false)
{
	$u_pt = unserialize($pt);
}
?>
<div class="wm_fbttposts_wrap">
	<form action="" method="post">
		<h1><?php echo __('Configurações FBTTPosts'); ?></h1>
		<hr><br>

		<input type="hidden" name="action" value="wm-fbttposts-save-config">
		<?php 
			wp_nonce_field( 'wm-fbttposts-save-config', 'wm_fbttposts_config' );
		?>

		<div class="wm-box-config">
			<h2><?php echo __('API Facebook'); ?></h2>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wm-fbAppId"><?php echo __('APP ID'); ?></label>
						</th>
						<td>
							<input name="wm-fbAppId" type="text" id="wm-fbAppId" value="<?php echo get_option('wm-fbAppId'); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wm-fbAppSecret"><?php echo __('APP Secret'); ?></label>
						</th>
						<td>
							<input name="wm-fbAppSecret" type="text" id="wm-fbAppSecret" value="<?php echo get_option('wm-fbAppSecret'); ?>" class="regular-text">
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="wm-box-config">
			<h2><?php echo __('API Twitter'); ?></h2>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label for="wm-ttConsumerKey"><?php echo __('Consumer Key'); ?></label>
						</th>
						<td>
							<input name="wm-ttConsumerKey" type="text" id="wm-ttConsumerKey" value="<?php echo get_option('wm-ttConsumerKey'); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wm-ttConsumerSecret"><?php echo __('Consumer Secret'); ?></label>
						</th>
						<td>
							<input name="wm-ttConsumerSecret" type="text" id="wm-ttConsumerSecret" value="<?php echo get_option('wm-ttConsumerSecret'); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wm-ttToken"><?php echo __('Token'); ?></label>
						</th>
						<td>
							<input name="wm-ttToken" type="text" id="wm-ttToken" value="<?php echo get_option('wm-ttToken'); ?>" class="regular-text">
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="wm-ttSecret"><?php echo __('Secret'); ?></label>
						</th>
						<td>
							<input name="wm-ttSecret" type="text" id="wm-ttSecret" value="<?php echo get_option('wm-ttSecret'); ?>" class="regular-text">
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="wm-box-config">
			<h2><?php echo __('Opções'); ?></h2>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row">
							<label><?php echo __('Post types compartilháveis'); ?></label>
						</th>
						<td>
							<p>
								<small><?php echo __('O padrão é post e page apenas.'); ?></small>
							</p>
							<select multiple style="width:100%;" name="wm-postTypes[]">
							<?php 
							foreach ($data['post_types'] as $k => $v) 
							{
								if($u_pt !== false)
								{
									echo '<option value="'.$v.'" '.FBTTPostsUtil::fbttposts_set_selected($u_pt, $v, true).'>'.$v.'</option>';
								}
								else
								{
									echo '<option value="'.$v.'">'.$v.'</option>';
								}
							}
							?>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label><?php echo __('Onde postar:'); ?></label>
						</th>
						<td>
							<fieldset>
								<label>
									<input type="radio" name="wm-optWhere" value="default" <?php echo FBTTPostsUtil::fbttposts_set_checked('default',get_option('wm-optWhere')); ?>>
									<span><?php echo __('No Facebook e no Twitter'); ?></span> 
								</label>
								<br>

								<label>
									<input type="radio" name="wm-optWhere" value="facebook" <?php echo FBTTPostsUtil::fbttposts_set_checked('facebook',get_option('wm-optWhere')); ?>> 
									<span><?php echo __('Apenas no Facebook'); ?></span>
								</label>
								<br>

								<label>
									<input type="radio" name="wm-optWhere" value="twitter" <?php echo FBTTPostsUtil::fbttposts_set_checked('twitter',get_option('wm-optWhere')); ?>> 
									<span><?php echo __('Apenas no Twitter'); ?></span>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for=""><?php echo __('Status:'); ?></label>
						</th>
						<td>
							<fieldset>
								<label>
									<input type="radio" name="wm-optContent" value="default" <?php echo FBTTPostsUtil::fbttposts_set_checked('default',get_option('wm-optContent')); ?>>
									<span><?php echo __('Titulo do post'); ?></span> 
								</label>
								<br>
								<label>
									<input type="radio" name="wm-optContent" value="excerpt" <?php echo FBTTPostsUtil::fbttposts_set_checked('title',get_option('wm-optContent')); ?>>
									<span><?php echo __('Resumo do post'); ?></span> 
								</label>
								<br>
								<label>
									<input type="radio" name="wm-optContent" value="custom" <?php echo FBTTPostsUtil::fbttposts_set_checked('custom',get_option('wm-optContent')); ?>> 
									<span><?php echo __('Conteúdo personalizado'); ?></span>
								</label>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for=""><?php echo __('Imagem a ser postada:'); ?></label>
						</th>
						<td>
							<fieldset>
								<label>
									<input type="radio" name="wm-optImage" value="default" <?php echo FBTTPostsUtil::fbttposts_set_checked('default',get_option('wm-optImage')); ?>>
									<span><?php echo __('Imagem destacada'); ?></span> 
								</label>
								<br>

								<label>
									<input type="radio" name="wm-optImage" value="custom" <?php echo FBTTPostsUtil::fbttposts_set_checked('custom',get_option('wm-optImage')); ?>> 
									<span><?php echo __('Imagem personalizada'); ?></span>
								</label>
								<br>
							</fieldset>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Salvar alterações'); ?>">
		</p>
	</form>
</div>