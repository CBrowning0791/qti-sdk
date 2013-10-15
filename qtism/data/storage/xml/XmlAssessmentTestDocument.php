<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml;

use qtism\data\AssessmentTest;
use \DOMDocument;

class XmlAssessmentTestDocument extends AssessmentTest implements IXmlDocument {
	
	/**
	 * The QTI version in use.
	 * 
	 * @var string
	 */
	private $version = '2.1';
	
	/**
	 * The XMLDocument object corresponding to the saved/loaded AssessmentTest.
	 * 
	 * @var XMLDocument
	 */
	private $xmlDocument = null;
	
	public function __construct($version = '2.1') {
		parent::__construct('assessmentTest', 'A QTI Assessment Test');
		$this->setXmlDocument(new XmlDocument($version, $this));
	}
	
	public function load($uri, $validate = false) {
		$this->getXmlDocument()->load($uri, $validate);
	}
	
	public function save($uri) {
		$this->getXmlDocument()->save($uri);
	}
	
	/**
	 * Validate the AssessmentTest XML document according to the relevant XSD schema.
	 * If $filename is provided, the file pointed by $filename will be used instead
	 * of the default schema.
	 */
	public function schemaValidate($filename = '') {
		if (empty($filename)) {
			// default xsd for AssessmentTest.
			$filename = Utils::getSchemaLocation('2.1');
		}
		
		$this->getXmlDocument()->schemaValidate($filename);
	}
	
	public function getUri() {
		return $this->getXmlDocument()->getUri();
	}
	
	public function setVersion($version) {
		$this->getXmlDocument()->setVersion($version);
	}
	
	public function getVersion() {
		return $this->getXmlDocument()->getVersion();
	}
	
	protected function setXmlDocument(XmlDocument $xmlDocument) {
		$this->xmlDocument = $xmlDocument;
	}
	
	public function getXmlDocument() {
		return $this->xmlDocument;
	}
}
