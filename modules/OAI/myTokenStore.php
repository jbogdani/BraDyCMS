<?php
/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since        Apr 3, 2014
 */

class myTokenStore implements \oaiprovider\tokens\TokenStore
{
  
  private function assertTable()
  {
    $sql = "
      CREATE TABLE IF NOT EXISTS oaitoken (
        `id`  integer,
        `token` varchar(255) NOT NULL,
        `data` longtext NOT NULL,
        `expirationdate` datetime NOT NULL,
        PRIMARY KEY (`id`)
      )
      ";
    R::exec($sql);
  }
  
  function storeToken($token, $data, $expirationdate)
  {
    //$this->assertTable();
    
    $tokObj = R::dispense('oaitoken');
    
    $tokObj->token = $token;
    $tokObj->data = $data;
    $tokObj->expirationdate = date('Y-m-d H:i:s', $expirationdate);
      
    $id = R::store($tokObj);
   
  }
  
  function fetchToken($token)
  {
    //$this->assertTable();
    $q = 'SELECT data FROM oaitoken WHERE token=' . $token;
    return R::getCell( 'SELECT data FROM oaitoken WHERE token=:token', array(':token' => $token) );
  }
  
}
?>
