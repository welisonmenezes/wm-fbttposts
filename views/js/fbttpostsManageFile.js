jQuery(document).ready(function() {
 
    fbttpostsManageFile();
    
});

var fbttpostsManageFile = function()
{
    var formfield;
    jQuery('.onetarek-upload-button').click(function() { 
        formfield = jQuery(this).prev('input');
        tb_show('','media-upload.php?TB_iframe=true');
        return false;
    });

    window.old_tb_remove = window.tb_remove;
    window.tb_remove = function() {
        window.old_tb_remove(); 
        formfield=null;
    };

    window.original_send_to_editor = window.send_to_editor;
    window.send_to_editor = function(html){

        var data = {
            'guid' : jQuery('img',html).attr('src'),
            'action' : 'wm-fbttposts-send-guid'
        };

        jQuery.post(ajaxurl, data, function(response)
        {
            jQuery('#wm-ajax-thumb').val(response);
        });

        if (formfield) {
            fileurl = jQuery('img',html).attr('src');
            jQuery('#wm-thumb-container').html('<img width="100" src="'+fileurl+'">');
            jQuery(formfield).val(fileurl);
            tb_remove();
        } else {
            window.original_send_to_editor(html);
        }
    };
};