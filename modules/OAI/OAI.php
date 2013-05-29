<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Apr 12, 2013
 */
 
class OAI_ctrl extends Controller
{
	public function run()
	{
		
		if (!file_exists('./sites/default/modules/oai/config.json'))
		{
			echo tr::get('no_oai_config_for_site');
			return;
		}
		
		$config = json_decode(file_get_contents('./sites/default/modules/oai/config.json'), true);
		
		
		require_once LIB_DIR . 'OAIprovider/endpoint.php';
		require_once MOD_DIR . 'OAI/myRepository.php';
		
		\oaiprovider\handleRequest($this->get, new myRepository($config, new DB()), null, 'xsl/oai2.xsl');
	}
}