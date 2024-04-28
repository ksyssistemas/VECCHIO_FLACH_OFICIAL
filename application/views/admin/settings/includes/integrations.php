<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="row">
	<h4><?php echo _l('settings_connection_btv');?></h4>
	<?php echo render_yes_no_option('integrado_btv','settings_integrado_btv'); ?>
	<!--<div class="col-md-6">
		<?php //echo render_input('settings[integration_btv_url_prod]','settings_integration_btv_url_prod',get_option('integration_btv_url_prod')); ?>
		<?php //echo render_input('settings[integration_btv_url_teste]','settings_integration_btv_url_teste',get_option('integration_btv_url_teste')); ?>
	</div>-->
	<div class="col-md-6">
		<?php echo render_input('settings[integration_btv_url]','settings_integration_btv_url',get_option('integration_btv_url')); ?>
		<?php //echo render_input('settings[integration_btv_url_hd]','settings_integration_btv_url_hd',get_option('integration_btv_url_hd')); ?>
	</div>
</div>
<p><?php echo _l('settings_integration_database');?></p>
<div class="row">
	<div class="col-md-3">
		<?php echo render_input('settings[integration_btv_base_dados_ip]','settings_integration_btv_base_dados_ip',get_option('integration_btv_base_dados_ip')); ?>
	</div>
	<div class="col-md-2">
		<?php echo render_input('settings[integration_btv_base_dados_user]','settings_integration_btv_base_dados_user',get_option('integration_btv_base_dados_user')); ?>
	</div>
	<div class="col-md-2">
		<?php echo render_input('settings[integration_btv_base_dados_password]','settings_integration_btv_base_dados_password','', 'password'); ?>
	</div>
	<div class="col-md-3">
		<?php echo render_input('settings[integration_btv_base_dados_db]','settings_integration_btv_base_dados_db',get_option('integration_btv_base_dados_db')); ?>
	</div>
	<div class="col-md-2">
		<?php echo render_input('settings[integration_btv_base_dados_port]','settings_integration_btv_base_dados_port',get_option('integration_btv_base_dados_port')); ?>
	</div>
</div>
<hr />
<div class="row">
	<h4><?php echo _l('settings_connection_logosystem');?></h4>
	<?php echo render_yes_no_option('integrado_logosystem','settings_integrado_logosystem'); ?>
	<div class="col-md-6">
		<?php echo render_input('settings[integration_url_crm_logosystem]','settings_integration_url_crm_logosystem',get_option('integration_url_crm_logosystem')); ?>
	</div>
	<div class="col-md-6">
		<?php echo render_input('settings[integration_url_logosystem]','settings_integration_url_logosystem',get_option('integration_url_logosystem')); ?>
	</div>
	<div class="col-md-12">
		<?php echo render_input('settings[integration_token_logosystem]','settings_integration_token_logosystem',get_option('integration_token_logosystem')); ?>
	</div>
</div>