<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license			MIT, See LICENSE file
 * @since			Apr 12, 2013
 */
 
class OAI_ctrl extends Controller
{
	public function run()
	{
		
		if (!file_exists('./sites/default/modules/metadata/MD_repository.inc'))
		{
			echo tr::get('no_oai_config_for_site');
			return;
		}
		
		unset($this->get['obj']);
		unset($this->get['method']);
		
		require_once './sites/default/modules/metadata/MD_repository.inc';
		require_once './vendor/oaiprovider-php/oaiprovider-php/endpoint.php';
		require_once MOD_DIR . 'OAI/myRepository.php';
		
		\oaiprovider\handleRequest($this->get, new myRepository(new MD_repository()), null, 'xsl/oai2.xsl');
	}
}