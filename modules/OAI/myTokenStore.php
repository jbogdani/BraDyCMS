<?php

/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2014 
 * @license			All rights reserved
 * @since				Apr 3, 2014
 */

class myTokenStore implements oaiprovider\tokens\TokenStore
{
  
  private function assertTable()
  {
    $sql = "
      CREATE TABLE IF NOT EXISTS oaitoken (
        `token` varchar(255) NOT NULL,
        `data` longtext NOT NULL,
        `expirationDate` datetime NOT NULL,
        PRIMARY KEY (`token`)
      )
      ";
    R::exec($sql);
  }
  
  function storeToken($token, $data, $expirationDate)
  {
    $this->assertTable();
    
    $tokObj = R::dispense('oaitoken');
    
    $tokObj->token = $token;
    $tokObj->data = $data;
    $tokObj->expirationDate = date('Y-m-d H:i:s', $expirationDate);
      
    $id = R::store($tokObj);
   
  }
  
  function fetchToken($token)
  {
    $this->assertTable();
    return R::getCell( 'SELECT data FROM oai_resumptiontoken WHERE token=:token', array(':token', $token) );
  }
  
}
?>
