<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			All rights reserved
 * @since			Apr 12, 2013
 */


use oaiprovider\xml\NS;


require_once LIB_DIR . 'OAIprovider/xml/europeana.php';
require_once LIB_DIR . 'OAIprovider/xml/dublincore.php';

use oaiprovider\xml\EuropeanaRecord;
use oaiprovider\xml\DublinCoreRecord;
use oaiprovider\Repository;
use oaiprovider\Header;


class myRepository implements Repository
{
	private $info, $db;
	
	public function __construct($info, DB $db)
	{
		$this->info = $info;
		$this->db = $db;
	}

	public function getIdentifyData() {
		return array(
				'repositoryName' => $this->info['repositoryName'],
				'baseURL' => $this->info['baseURL'],
				'protocolVersion' => $this->info['protocolVersion'],
				'adminEmail' => $this->info['adminEmail'],
				'earliestDatestamp' => '2013-04-01',
				'deletedRecord' => 'persistent',
				'granularity' => 'YYYY-MM-DDThh:mm:ssZ');
	}

	public function getMetadataFormats($identifier = null) {
		return array(
				array(
						'metadataPrefix' => 'oai_dc',
						'schema' => 'http://www.openarchives.org/OAI/2.0/oai_dc.xsd',
						'metadataNamespace' => 'http://www.openarchives.org/OAI/2.0/oai_dc/',
				),
				array(
						'metadataPrefix' => 'ese',
						'schema' => 'http://www.europeana.eu/schemas/ese/ESE-V3.3.xsd',
						'metadataNamespace' => 'http://www.europeana.eu/schemas/ese/',
				),
		);
	}

	public function getSets() {
		return $this->info['sets'];
	}

	public function getIdentifiers($from, $until, $set, $last_identifier, $max_results)
	{
		$sql = "SELECT `" . $this->info['table']['id'] . "` FROM `" . $this->info['table']['name'] . "`";

		$where = array();
		$values = array();
		
		if ($from)
		{
			$where[] = "lastchanged >= :lastchanged_from";
			$values[':lastchanged_from'] = date('Y-m-d', $from);
		}
		
		if ($until)
		{
			$where[] = "lastchanged <= :lastchanged_until";
			$values[':lastchanged_until'] = date('Y-m-d', $until);
		}
		
		if ($set)
		{
			$where[] = $this->info['table']['category'] . " = :category";
			$values[':category'] = $set;
		}
		
		if ($last_identifier)
		{
			$where[] = "id > :last_identifier";
			$values[':last_identifier'] = $last_identifier;
		}

		if (count($where))
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " ORDER BY id ASC";

		if ($max_results)
		{
			$sql .= " LIMIT " . $max_results;
		}
		
		$ids = array();
		
		$res = $this->db->executeQuery($sql, $values, 'read');
		
		foreach($res as $row)
		{
			$ids[] = $row[$this->info['table']['id']];
		}
		return $ids;
	}

	public function getHeader($identifier)
	{
		$identifier = str_replace('oai:' . $_SERVER['HTTP_HOST'] . ':article/', null, $identifier);
		
		$sql = "SELECT " .
					"`id`, `" . $this->info['table']['id'] . "`, `" . $this->info['table']['lastchanged'] . "`, `" . $this->info['table']['category'] . "`, `" . $this->info['table']['deleted'] . "` " .
				"FROM `" . $this->info['table']['name'] . "` " .
				"WHERE `" . $this->info['table']['id'] . "`= :id";
		
		
		$result = $this->db->executeQuery($sql, array(':id' => $identifier), 'read');
		$row = $this->clean($result[0]);
		
		$header = new Header();
		
		$header->identifier = 'oai:' . $_SERVER['HTTP_HOST'] . ':article/' . $row['id'];
		
		$header->datestamp = strtotime($row[$this->info['table']['lastchanged']]);
		
		if ($row[$this->info['table']['category']])
		{
			$header->setSpec = array($row[$this->info['table']['category']]);
		}
		
		$header->deleted = ($row[$this->info['table']['deleted']] == 0);
		
		return $header;
	}

	public function getMetadata($metadataPrefix, $identifier)
	{
		$identifier = str_replace('oai:' . $_SERVER['HTTP_HOST'] . ':article/', null, $identifier);
		$sql = "SELECT * FROM " . $this->info['table']['name'] . " WHERE " . $this->info['table']['id'] . "= :id";
		
		$res = $this->db->executeQuery($sql, array(':id' => $identifier), 'read');
		
		$row = $this->clean($res[0]);
		
		if ($row[$this->info['table']['deleted']] === 0)
		{
			return null;
		}
		
		switch($metadataPrefix)
		{
			case 'oai_dc':
				
				$dcrec = new DublinCoreRecord();
				//http://dublincore.org/documents/dcmi-terms/#terms-title
				$dcrec->addNS(NS::DC, 'title', $row[$this->info['table']['title']]);
				if ($row[$this->info['table']['translated_title']])
				{
					$dcrec->addNS(NS::DC, 'title', $row[$this->info['table']['translated_title']]);
				}
				
				$dcrec->addNS(NS::DC, 'creator', $row[$this->info['table']['creator']]);
				//http://dublincore.org/documents/dcmi-terms/#terms-description
				$dcrec->addNS(NS::DC, 'description', $row[$this->info['table']['description']]);
				// http://dublincore.org/documents/dcmi-terms/#terms-publisher
				$dcrec->addNS(NS::DC, 'publisher', $this->info['publisher']);
				//http://dublincore.org/documents/dcmi-terms/#terms-date
				$dcrec->addNS(NS::DC, 'date', $row[$this->info['table']['lastchanged']]);
				//http://dublincore.org/documents/dcmi-terms/#terms-type
				// http://info-uri.info/registry/OAIHandler?verb=GetRecord&metadataPrefix=reg&identifier=info:eu-repo/
				$dcrec->addNS(NS::DC, 'type', 'info:eu-repo/semantics/article');
				
				$dcrec->addNS(NS::DC, 'identifier', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $row['text_id']);
				
				$dcrec->addNS(NS::DC, 'identifier', $this->info['doi_prefix'] . '/' . $this->info['journal_suffix'] . '.' . $row[$this->info['table']['id']]);
				
				$dcrec->addNS(NS::DC, 'identifier', $this->info['issn']);
				
				return $dcrec->toXml();
			case 'ese':
				$eserec = new EuropeanaRecord();
				$eserec->addNS(NS::DC, 'title', $row[$this->info['table']['title']]);
				if ($row[$this->info['table']['translated_title']])
				{
					$eserec->addNS(NS::DC, 'title', $row[$this->info['table']['translated_title']]);
				}
				$eserec->addNS(NS::DC, 'description', $row[$this->info['table']['description']]);
				
				$eserec->addNS(NS::DC, 'title', $row[$this->info['table']['title']]);
				$eserec->addNS(NS::DC, 'description', $row[$this->info['table']['description']]);
				$eserec->addNS(NS::DC, 'publisher', $this->info['publisher']);
				$eserec->addNS(NS::DC, 'date', $row[$this->info['table']['lastchanged']]);
				$eserec->addNS(NS::DC, 'type', 'info:eu-repo/semantics/article');
				
				$eserec->addNS(NS::DC, 'identifier', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $row['text_id']);
				$eserec->addNS(NS::DC, 'identifier', $this->info['doi_prefix'] . '/' . $row[$this->info['table']['id']]);
				$eserec->addNS(NS::DC, 'identifier', $this->info['doi_prefix'] . '/' . $this->info['journal_suffix'] . '.' . $row[$this->info['table']['id']]);
				$eserec->addNS(NS::DC, 'identifier', $this->info['issn']);
				
				return $eserec->toXml();
		}
	}
	
	private function clean($data)
	{
		if (is_array($data))
		{
			foreach ($data as &$d)
			{
				$d = $this->clean($d);
			}
		}
		else
		{
			$data = trim(strip_tags($data));
		}
		
		return $data;
	}

}