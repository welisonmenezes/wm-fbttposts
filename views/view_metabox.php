<?php
// post id
$p_id 		= get_the_ID();
// post title
$p_title   	= get_the_title();
// post excerpt
$p_excerpt 	= get_the_excerpt();
// post status
$p_status 	= get_post_status($p_id);
// post link
$p_link     = get_permalink();

// status a ser compartilhado
$p_text = (get_option('wm-optContent')=='excerpt') ? $p_excerpt : $p_title;

// post thumbnail info
if(get_option('wm-optImage')=='default')
{
	$p_thumb_id 	= get_post_thumbnail_id($p_id);
	$thumb 			= FBTTPOstsUtil::fbttposts_get_info_file($p_thumb_id);
	$p_thumb 		= $thumb['post_image'];
	$source_thumb 	= $thumb['post_source'];
	$mime_thumb 	= $thumb['post_mime'];
	$name_thumb 	= $thumb['post_file'];
}
else
{
	$p_thumb_id 	= null;
	$thumb 			= null;
	$p_thumb 		= null;
	$source_thumb 	= null;
	$mime_thumb 	= null;
	$name_thumb 	= null;
}

?>
<div class="wm_fbttposts_wrap">
	<?php $ajax_nonce = wp_create_nonce("wm-fbttposts-share-post"); ?>

	<?php
	if(get_post_meta($p_id, 'wm-fbttposts-publish', true) === "publish")
	{
	?>
		<h3 class="wm-alert">Publique o post antes de compartilha-lo.</h3>
	<?php 
	}
	?>

	<?php 
	if( get_option('wm-optContent')=='custom' || get_option('wm-optImage')=='custom' ) 
	{ 
	?>
	<div class="wm-form postbox">
		<div class="inside">
			<?php 
			if( get_option('wm-optContent')=='custom' ) 
			{ 
			?>
			<p>
				<label class="">Digite o status a ser compartilhado:</label>
				<textarea rows="1" cols="40" name="wm-fbttposts-status" id="wm-status"><?php echo get_post_meta($p_id, 'wm-fbttposts-status', true); ?></textarea>
				<input type="hidden" id="wm-ajax-thumb" name="wm-fbttposts-json" value='<?php echo get_post_meta($p_id, 'wm-fbttposts-json', true); ?>'>
			</p>
			<?php 
			}
			if( get_option('wm-optImage')=='custom' ) 
			{ 
			?>
			<p>
				<span id="wm-thumb-container">
					<?php 
						$wm_image = get_post_meta($p_id, 'wm-fbttposts-image', true);
						if(!empty($wm_image))
						{
					?>
							<img src="<?php echo $wm_image; ?>" width="100" />
					<?php
						}
					?>
				</span>
				<label class="">Selecione a imagem a ser compartilhada:</label>
				<input type="text"  name="wm-fbttposts-image" disabled="disabled" value="<?php echo $wm_image; ?>" size="40" />
				<input type="button" class="onetarek-upload-button button" value="Upload Image" />
			</p>
			<?php 
		    }
		    ?>
		</div>
	</div>
	<?php 
    }
    ?>

	<?php 
	if(get_post_meta($p_id, 'wm-fbttposts-publish', true) === "publish")
	{
	?>
		<div id="fbttposts-response" class="wm-alert">
			<?php echo __('Este Post já foi compartilhado. Deseja compartilhar novamente?'); ?>
		</div>
		<div id="fbttposts-links" class="fbtt-links"></div>
		<div class="">
			<p>
				<span class="hide load">
					<?php echo __('aguarde...'); ?>
				</span>
				<span class="button button-primary" id="fbttposts-submit">
					<?php echo __("Sim compartilhar post novamente?"); ?>
				</span>
			</p>
		</div>
	<?php 
	}
	else
	{
	?>
		<div id="fbttposts-response">
			<span class="hide">
				<?php echo __('Post compartilhado com sucesso! Deseja compartilhar novamente? '); ?>
			</span>
		</div>
		<div id="fbttposts-links" class="fbtt-links"></div>
		<div class="">
			<p>
				<span class="hide load">
					<?php echo __('aguarde...'); ?>
				</span>
				<span class="button button-primary" id="fbttposts-submit">
					<?php echo __("Compartilhar post."); ?>
				</span>
			</p>
		</div>
	<?php 
	}
	?>
</div>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	var full_url = '<?php echo FBTTPOSTS_FULL_URL; ?>';
	var status   = '<?php echo $p_status; ?>';
	var data = {
		action		: 'wm-fbttposts-share-post',
		security	: '<?php echo $ajax_nonce; ?>',
		post_id     : '<?php echo $p_id; ?>',
		post_status : '<?php echo $p_text; ?>',
		post_image  : '<?php echo $p_thumb; ?>',
		post_source : '<?php echo $source_thumb; ?>',
		post_mime   : '<?php echo $mime_thumb; ?>',
		post_file   : '<?php echo $name_thumb; ?>',
		post_link   : '<?php echo $p_link; ?>'//'http://www.welisonmenezes.com.br'
	};

	jQuery('#fbttposts-submit').on('click', function()
	{
		// elements
		var t = jQuery(this);
		var p = t.parent().parent();

		// view callback
		t.hide();
		p.find('.load').show();
		jQuery('#fbttposts-links').html('');

		// valid if post is published
		if(status!='publish') 
		{
			wm_fbttposts_messages(p,t,'<?php echo __("Você precisa publicar o post antes de compartilha-lo."); ?>');
			return false;
		}

		// get infos of the image thumb
		<?php 
		if( get_option('wm-optImage')=='custom' ) 
		{ 
		?>
			if($.trim(jQuery('#wm-ajax-thumb').val())!='')
			{	
				var ajaxThumb = $.parseJSON(jQuery('#wm-ajax-thumb').val());
				data.post_file = ajaxThumb.post_file;
				data.post_image = ajaxThumb.post_image;
				data.post_mime = ajaxThumb.post_mime;
				data.post_source = ajaxThumb.post_source;
			}
		<?php 
		}
		?>

		// get infos of the status
		<?php 
		if( get_option('wm-optContent')=='custom' ) 
		{ 
		?>
			if($.trim($('#wm-status').val()) != '')
			{
				data.post_status = $.trim($('#wm-status').val());
			}
			else
			{
				data.post_status = '';
			}
		<?php 
		}
		?>
		//console.log(data);
		// validations
		if($.trim(data.post_file) == '' || $.trim(data.post_image) == '' || $.trim(data.post_mime) == '' || $.trim(data.post_source) == '')
		{
			wm_fbttposts_messages(p,t,'<?php echo __("Por favor selecione uma imagem para compartilhar."); ?>');
			return false;
		}

		if($.trim(data.post_status) == '')
		{
			wm_fbttposts_messages(p,t,'<?php echo __("Por favor informe o status para compartilhar."); ?>');
			return false;
		}

		// ajax to publih post
		$.post(ajaxurl, data, function(response) 
		{
			if(response)
			{
				jQuery('#fbttposts-response').html(response);
				try
				{
					ojb_r = $.parseJSON(response);
					if($.isPlainObject(ojb_r))
					{
						if(ojb_r.tt_can && ojb_r.fb_can)
						{
							p.find('.load').hide();	
							t.html('<?php echo __("Sim compartilhar post novamente?"); ?>').show();
							jQuery('#fbttposts-response')
								.addClass('wm-alert')
								.html('<?php echo __("Post compartilhado com sucesso. Deseja compartilhar novamente?"); ?>');
						}
					}
				}catch(e)
				{
					jQuery('#fbttposts-response')
					.html('<?php echo __("O Post não foi compartilhado. Por favor verificar se você está conectado nas redes socias."); ?>')
					.addClass('wm-alert');

					jQuery('#fbttposts-links').html(response);

					p.find('.load').hide();	
					t.html('<?php echo __("Tentar compartilhar post novamente."); ?>').show();
				}
			}
			else
			{
				jQuery('#fbttposts-response')
				.html('<?php echo __("Por favor certifique-se de que as informações de acesso às redes sociais estejam corretas na configuração do plugin."); ?>')
				.addClass('wm-alert');

				p.find('.load').hide();	
				t.html('<?php echo __("Tentar compartilhar post novamente."); ?>').show();
			}
		});
	});
});

// show message errors
var wm_fbttposts_messages = function(p, t, msg)
{
	p.find('.load').hide();
	t.show();
	jQuery('#fbttposts-response')
		.addClass('wm-alert')
		.html(msg);
}
</script>