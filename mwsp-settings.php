<?php
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
?>
<div class="wrap">
<h2>Signpost Settings</h2>
<form method="post" action="options.php">
<?php settings_fields( 'mwsp-settings-group' );?>
<?php do_settings_sections( 'mwsp-settings-group' );?>

<fieldset>
<legend>Signpost Details.</legend>
<table class="form-table">
    <tr valign="top">
    <th scope="row">API Key :</th>
    <td>
    	<input size="45" type="text" name="mwsp_api_key" value="<?php echo esc_attr( get_option('mwsp_api_key') ); ?>" />
    </td>
    </tr>
     
    <tr valign="top">
    <th scope="row">Merchant ID :</th>
    <td><input size="45" type="text" name="mwsp_merchant_id" value="<?php echo esc_attr( get_option('mwsp_merchant_id') ); ?>" /></td>
    </tr>
	
	<tr valign="top">
    <th scope="row">Sandbox :</th>
    <td>
	<select name="mwsp_sandbox">
	<option value="No"<?php if(esc_attr( get_option('mwsp_sandbox') )=='No'){echo ' selected="selected"';}?>>No</option>
	<option value="Yes"<?php if(esc_attr( get_option('mwsp_sandbox') )=='Yes'){echo ' selected="selected"';}?>>Yes</option>
	</select>	
	</td>
    </tr>
	
	<tr valign="top">
    <th scope="row">Shortcode form css :</th>
    <td>	
	<textarea name="mwsp_sc_css" rows="4" cols="44"><?php echo esc_attr( get_option('mwsp_sc_css') ); ?></textarea>
	<br />
	<small>Shortcode: [mw_sp_form title="Add title here" fields="name,phone"]</small>
	</td>
    </tr>
    
</table>
</fieldset>
<?php submit_button('Save Settings'); ?>
</form>
</div>