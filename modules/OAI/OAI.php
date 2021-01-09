<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Apr 12, 2013
 */
 
class OAI_ctrl extends Controller
{
  public function run()
  {
    if(!file_exists('./sites/default/modules/metadataRepo/metadata.json'))
    {
      echo tr::get('no_oai_config_for_site');
      return;
    }
    
    $metadata = new Metadata('./sites/default/modules/metadataRepo/metadata.json');

    require_once LIB_DIR . 'vendor/oaiprovider-php/endpoint.php';
    require_once MOD_DIR . 'OAI/myRepository.php';
    require_once MOD_DIR . 'OAI/myTokenStore.php';

    unset($this->get['obj']);
    unset($this->get['method']);

    \oaiprovider\handleRequest($this->get, new myRepository($metadata), new myTokenStore(), 'xsl/oai2.xsl');
  }
}