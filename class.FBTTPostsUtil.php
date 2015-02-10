<?php 
class FBTTPostsUtil
{
	/**
	 * render html file FBTTPosts
	 *
	 * @param string - directory file html
	 * @param boolean - optional - echo or return 
	 * @param array - optional - generic data
	 *
	 * @return content string file or void
	 */
	public static function fbttposts_render_view($file, $show = true, $data = array())
	{
		ob_start();
		$data = $data;
		include $file;
		$out = ob_get_clean();
		if($show)
			echo $out;
		else
			return $out;
	}

	/**
	 * set checked if equals params
	 *
	 * @param string - target value
	 * @param string - value of options
	 *
	 * @return string
	 */
	public static function fbttposts_set_checked($target, $value)
	{
		if($target == $value)
		{
			return "checked='checked'";
		}
		return "";
	}

	public static function fbttposts_set_selected($target, $value, $multiple=false)
	{	
		if($multiple)
		{
			//in_array
			if(in_array($value, $target))
			{
				return "selected='selected'";
			}
		}
		else
		{
			if($target == $value)
			{
				return "selected='selected'";
			}
		}
		
		return "";
	}

	public static function fbttposts_get_info_file($file_id)
	{
		$thumb = array(
			'post_image' => null,
			'post_source' => null,
			'post_mime' => null,
			'post_file' => null
		);

		$thumb['post_image']  = wp_get_attachment_url($file_id);

		if(!empty($thumb['post_image']))
		{
			// details thumbnail
			$ar_tf =  wp_get_attachment_metadata($file_id);
			// thumbnail file name
			$tf = (isset($ar_tf['file']))?$ar_tf['file']:'';
			// details upload dir
			$ar_bd = wp_upload_dir();
			// base directory path
			$bd = $ar_bd['basedir'];

			// full path thumbnail
			$source_thumb = $bd.DIRECTORY_SEPARATOR.$tf;
			$source_thumb = str_replace("\\", DIRECTORY_SEPARATOR, $source_thumb);
			$source_thumb = str_replace("/", DIRECTORY_SEPARATOR, $source_thumb);
			$thumb['post_source'] = rawurlencode($source_thumb);

			// details thumbnail
			$thumb_size = getimagesize($thumb['post_image']);
			// thumbnail mime type
			$thumb['post_mime'] = $thumb_size['mime'];

			// name thumbnail
			$name_thumb = explode('/', $ar_tf['file']);
			$thumb['post_file'] = end($name_thumb);
		}
		//var_dump($thumb);
		return $thumb;
	}
}
?>