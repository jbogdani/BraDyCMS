<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2013
 * @license			MIT, See LICENSE file
 * @since				Apr 12, 2013
 * @uses				R readbean onject
 */


use oaiprovider\xml\NS;


require_once 'vendor/oaiprovider-php/oaiprovider-php/xml/europeana.php';
require_once 'vendor/oaiprovider-php/oaiprovider-php/xml/dublincore.php';

use oaiprovider\xml\EuropeanaRecord;
use oaiprovider\xml\DublinCoreRecord;
use oaiprovider\Repository;
use oaiprovider\Header;


class myRepository implements Repository
{
	private $repo, $db;
	
	public function __construct(Metadata $metadata)
	{
		// info
		$this->repo = $metadata;
	}

	public function getIdentifyData() {
		return array(
				'repositoryName' => $this->repo->getRepositoryName(),
				'baseURL' => $this->repo->getOAPMH_URL(),
				'protocolVersion' => $this->repo->getProtocolVersion(),
				'adminEmail' => $this->repo->getAdminEmail(),
				'earliestDatestamp' => $this->repo->getEarliestDate(),
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
		return $this->repo->getSets();
	}

	public function getIdentifiers($from, $until, $set, $last_identifier, $max_results)
	{
		$sql = "SELECT `" . $this->repo->getTable('id') . "` FROM `" . $this->repo->getTable('name') . "`";

		$where = array();
		$values = array();
		
		if ($from)
		{
			$where[] = '`' . $this->repo->getTable('updated') . "` = :lastchanged_from";
			$values[':lastchanged_from'] = date('Y-m-d', $from);
		}
		
		if ($until)
		{
			$where[] = '`' . $this->repo->getTable('updated') . "` = :lastchanged_until";
			$values[':lastchanged_until'] = date('Y-m-d', $until);
		}
		
		if ($set)
		{
			$where[] = '`' . $this->repo->getTable('category') . "` = :category";
			$values[':category'] = $set;
		}
		
		if ($last_identifier)
		{
			$where[] = '`' . $this->repo->getTable('id') . "` = :last_identifier";
			$values[':last_identifier'] = $last_identifier;
		}

		if (count($where))
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}

		$sql .= " ORDER BY `id` ASC";

		if ($max_results)
		{
			$sql .= " LIMIT " . $max_results;
		}
		
		$ids = array();
		
		$res = R::getAll($sql, $values);
		
		foreach($res as $row)
		{
			$ids[] = $row[$this->repo->getTable('id')];
		}
		return $ids;
	}

	public function getHeader($identifier)
	{
		$identifier = str_replace('oai:' . $_SERVER['HTTP_HOST'] . ':article/', null, $identifier);
		
		$sql = "SELECT " .
					"`id`, `" . $this->repo->getTable('id') . "`, `" . $this->repo->getTable('lastchanged') . "`, `" . $this->repo->getTable('category') . "`, `" . $this->repo->getTable('deleted') . "` " .
				"FROM `" . $this->repo->getTable('name') . "` " .
				"WHERE `" . $this->repo->getTable('id') . "`= :id";
		
		
		$result = R::getAll($sql, array(':id' => $identifier));
		$row = $this->clean($result[0]);
		
		$header = new Header();
		
		$header->identifier = 'oai:' . $_SERVER['HTTP_HOST'] . ':article/' . $row['id'];
		
		$header->datestamp = strtotime($row[$this->repo->getTable('lastchanged')]);
		
		if ($row[$this->repo->getTable('category')])
		{
			$header->setSpec = array($row[$this->repo->getTable('category')]);
		}
		
		$header->deleted = ($row[$this->repo->getTable('deleted')] == 0);
		
		return $header;
	}

	public function getMetadata($metadataPrefix, $identifier)
	{
		$identifier = str_replace('oai:' . $_SERVER['HTTP_HOST'] . ':article/', null, $identifier);
		$sql = "SELECT * FROM " . $this->repo->getTable('name') . " WHERE " . $this->repo->getTable('id') . "= :id";
		
		$res = R::getAll($sql, array(':id' => $identifier));
		
		$row = $this->clean($res[0]);
		
		if ($row[$this->repo->getTable('deleted')] === 0)
		{
			return null;
		}
		
		switch($metadataPrefix)
		{
			case 'oai_dc':
				
				$dcrec = new DublinCoreRecord();
				//http://dublincore.org/documents/dcmi-terms/#terms-title
				$dcrec->addNS(NS::DC, 'title', $row[$this->repo->getTable('title')]);
				if ($row[$this->repo->getTable('translated_title')])
				{
					$dcrec->addNS(NS::DC, 'title', $row[$this->repo->getTable('translated_title')]);
				}
				
				$dcrec->addNS(NS::DC, 'creator', $row[$this->repo->getTable('creator')]);
				//http://dublincore.org/documents/dcmi-terms/#terms-description
				$dcrec->addNS(NS::DC, 'description', html_entity_decode($row[$this->repo->getTable('description')]));
				// http://dublincore.org/documents/dcmi-terms/#terms-publisher
				$dcrec->addNS(NS::DC, 'publisher', $this->repo->getTable('publisher'));
				//http://dublincore.org/documents/dcmi-terms/#terms-date
				$dcrec->addNS(NS::DC, 'date', $row[$this->repo->getTable('lastchanged')]);
				//http://dublincore.org/documents/dcmi-terms/#terms-type
				// http://info-uri.info/registry/OAIHandler?verb=GetRecord&metadataPrefix=reg&identifier=info:eu-repo/
				$dcrec->addNS(NS::DC, 'type', 'info:eu-repo/semantics/article');
				
				$dcrec->addNS(NS::DC, 'identifier', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $row['textid']);
				$dcrec->addNS(NS::DC, 'identifier', $this->repo->getDoiPrefix() . '' . $row[$this->repo->getTable('id')]);
				$dcrec->addNS(NS::DC, 'identifier', $this->repo->getISSN());
				
				return $dcrec->toXml();
			case 'ese':
				$eserec = new EuropeanaRecord();
				$eserec->addNS(NS::DC, 'title', $row[$this->repo->getTable('title')]);
				if ($row[$this->repo->getTable('translated_title')])
				{
					$eserec->addNS(NS::DC, 'title', $row[$this->repo->getTable('translated_title')]);
				}
				$eserec->addNS(NS::DC, 'description', html_entity_decode($row[$this->repo->getTable('description')]));
				
				$eserec->addNS(NS::DC, 'title', $row[$this->repo->getTable('title')]);
				$eserec->addNS(NS::DC, 'description', html_entity_decode($row[$this->repo->getTable('description')]));
				$eserec->addNS(NS::DC, 'publisher', $this->repo->getPublisher());
				$eserec->addNS(NS::DC, 'date', $row[$this->repo->getTable('lastchanged')]);
				$eserec->addNS(NS::DC, 'type', 'info:eu-repo/semantics/article');
				
				$eserec->addNS(NS::DC, 'identifier', 'http://' . $_SERVER['HTTP_HOST'] . '/' . $row['textid']);
				$eserec->addNS(NS::DC, 'identifier', $this->repo->getDoiPrefix() . '' . $row[$this->repo->getTable('id')]);
				$eserec->addNS(NS::DC, 'identifier', $this->repo->getISSN());
				
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