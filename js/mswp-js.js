if(!window.jQuery)
{
   console.log('Signpost plugin require jquery library.');
}

jQuery(document).ready(function($) {
var ajaxurl = mswp_js_val.mwsp_ajax_url;
var plugins_url = mswp_js_val.mwsp_plugins_url;

var loading_img = '<img src="'+plugins_url+'/images/ajax-loader.gif" alt=""> Loading...';

jQuery("#mwsp_form").submit(function(e){
	e.preventDefault();
	
	var data = {
		"action": "mwsp_post_signpost"
	};
	
	data = jQuery(this).serialize() + "&" + jQuery.param(data);
	jQuery('.mwsp_status_msg').html(loading_img);
	
	jQuery.ajax({
	   type: "POST",
	   url: ajaxurl,
	   data: data,
	   datatype: "json",
	   success: function(data){
		   //console.log(data);
		   if(data!=''){
				try {
					var data = jQuery.parseJSON(data);
					var status = data.status;
					var msg = data.msg;
					
					jQuery('.mwsp_status_msg').html(msg);					
					
				}catch(err) {
					var err_msg = err.message;
					jQuery('.mwsp_status_msg').html('<div class="mwsp_err_msg">Error occured please try again.</div>');
				}						
		   }else{
			   jQuery('.mwsp_status_msg').html('<div class="mwsp_err_msg">Error occured please try again.</div>');
		   }
	   },
	   error:function(jqXHR, textStatus, errorThrown){
		   jQuery('.mwsp_status_msg').html('<div class="mwsp_err_msg">Error occured please try again.</div>');
	   },
	});
});

});