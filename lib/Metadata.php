<?php
/**
 * Abstract class for metadata structured information
 * This class should be implemented and populated with data from single by ./sites/default/modules/metadata/MD_repositories.inc classes
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Jun 6, 2013
 *
 */


class Metadata
{
    /**
     *
     * @var array Main container of all metadata info
     */
    protected $metadata;


    public function __construct($path2metadata)
    {
        $this->metadata = array();

        if (file_exists($path2metadata)) {
            $this->metadata = json_decode(file_get_contents($path2metadata), true);
        }
    }

    public function __get($name)
    {
        return $this->metadata[$name];
    }

    /**
     * Returns the repository (Review) name
     * @return string:
     */
    public function getRepositoryName()
    {
        return $this->metadata['repositoryName'];
    }

    /**
     * Returns repository short name
     * @return string:
     */
    public function getRepositoryShortName()
    {
        return $this->metadata['repositoryShortName'];
    }

    /**
     * Returns the main page's URL for the present repository (Review)
     * @return string:
     */
    public function getURL()
    {
        return $this->metadata['URL'];
    }

    /**
     * Returns the OAI-PMH URL for the present repository (Review)
     * @return string:
     */
    public function getOAPMH_URL()
    {
        return $this->metadata['baseURL'];
    }

    /**
     * Returns the OAI-PMH protocol version or, if missing, the default version: 2.0
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->metadata['protocolVersion'] ? $this->metadata['protocolVersion']  : "2.0";
    }

    /**
     * Returns OAI-PMH administrator email address
     * @return string
     */
    public function getAdminEmail()
    {
        return $this->metadata['adminEmail'];
    }

    /**
     * Returns array of available sets
     * @return array
     */
    public function getSets()
    {
        return $this->metadata['sets'];
    }

    /**
     * Returns table mapping information. If $el is true not all table array will be returned, but only the matching element
     * @param string $el table element to return
     * @return multitype: array|string
     */
    public function getTable($el = false)
    {
        return $el ? $this->metadata['table'][$el] : $this->metadata['table'];
    }

    /**
     * Returns publisher name
     * @return string
     */
    public function getPublisher()
    {
        return $this->metadata['publisher'];
    }

    /**
     * Returns DOI prefix (Editor prefix + journal prefix)
     * @return multitypestring
     */
    public function getDoiPrefix()
    {
        return $this->metadata['doiPrefix'];
    }

    /**
     * Returns journal DOI prefix
     * @return multitypestring
     */
    public function getJournalDoi()
    {
        return $this->metadata['journalDoi'];
    }

    /**
     * Returns ISSN number
     * @return string
     */
    public function getISSN()
    {
        return $this->metadata['issn'] ? $this->metadata['issn'] : $this->metadata['eissn'];
    }

    /**
     * Returns EISSN number
     * @return string
     */
    public function getEISSN()
    {
        return $this->metadata['eissn'];
    }

    /**
     * Returns repository darliest date
     * @return string
     */
    public function getEarliestDate()
    {
        return $this->metadata['earliestDate'];
    }

    public function getCopyright($el = false)
    {
        return $el ? $this->metadata['copyright'][$el] : $this->metadata['copyright'];
    }
}
