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
		
		if (!file_exists('./sites/default/modules/oai/MD_repository.inc'))
		{
			echo tr::get('no_oai_config_for_site');
			return;
		}
		
		require_once './sites/default/modules/oai/MD_repository.inc';
		require_once LIB_DIR . 'OAIprovider/endpoint.php';
		require_once MOD_DIR . 'OAI/myRepository.php';
		
		\oaiprovider\handleRequest($this->get, new myRepository(new MD_repository(), new DB()), null, 'xsl/oai2.xsl');
	}
}