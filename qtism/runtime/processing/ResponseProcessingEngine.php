<?php

namespace qtism\runtime\processing;

use qtism\runtime\rules\RuleEngine;
use qtism\data\storage\xml\XmlResponseProcessingDocument;
use qtism\data\processing\ResponseProcessing;
use qtism\data\QtiComponent;
use qtism\runtime\common\State;
use qtism\runtime\common\AbstractEngine;
use qtism\data\storage\StorageException;
use \InvalidArgumentException;

class ResponseProcessingEngine extends AbstractEngine {
	
	/**
	 * An array used to map template URIs with actual location
	 * of the templates. This array has keys containing the URL
	 * of the template. The related values is the location of the
	 * template to be used.
	 * 
	 * @var array
	 */
	private $templateMapping = array();
	
	/**
	 * Create a new ResponseProcessingEngine object.
	 * 
	 * @param QtiComponent $responseProcessing
	 * @param State $context
	 * @throws InvalidArgumentException If $responseProcessing is not a ResponseProcessing object.
	 */
	public function __construct(QtiComponent $responseProcessing, State $context = null) {
		parent::__construct($responseProcessing, $context);
		
		$templateDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR; 
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct', $templateDir . '2_1' . DIRECTORY_SEPARATOR . 'match_correct.xml');
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response', $templateDir . '2_1' . DIRECTORY_SEPARATOR . 'map_response.xml');
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response_point', $templateDir . '2_1' . DIRECTORY_SEPARATOR . 'map_response_point.xml');
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p0/rptemplates/match_correct', $templateDir . '2_0' . DIRECTORY_SEPARATOR . 'match_correct.xml');
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response', $templateDir . '2_0' . DIRECTORY_SEPARATOR . 'match_correct.xml');
		$this->addTemplateMapping('http://www.imsglobal.org/question/qti_v2p0/rptemplates/map_response_point', $templateDir . '2_0' . DIRECTORY_SEPARATOR . 'map_response_point.xml');
	}
	
	/**
	 * Set the ResponseProcessing object to be executed.
	 * 
	 * @param QtiComponent A ResponseProcessing object.
	 * @throws InvalidArgumentException If $responseProcessing is not a ResponseProcessing object.
	 */
	public function setComponent(QtiComponent $responseProcessing) {
		if ($responseProcessing instanceof ResponseProcessing) {
			parent::setComponent($responseProcessing);
		}
		else {
			$msg = "The ResponseProcessingEngine class only accepts ResponseProcessing objects to be executed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Add a template mapping.
	 * 
	 * @param string $uri The template URI (Uniform Resource Identifier).
	 * @param string $url The actual template URL, i.e. where to find the file containing the template markup.
	 * @throws InvalidArgumentException If $uri or $url are not strings.
	 */
	public function addTemplateMapping($uri, $url) {
		
		if (gettype($uri) !== 'string') {
			$msg = "The uri argument must be a string, '" . gettype($uri) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		if (gettype($url) !== 'string') {
			$msg = "The url argument must be a string, '" . gettype($uri) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		$templateMapping = &$this->getTemplateMapping();
		$templateMapping[$uri] = $url;
	}
	
	/**
	 * Remove a template mapping for a given $uri. If no template mapping
	 * is found for $uri, nothing happens.
	 * 
	 * @param string $uri The $uri you want to remove the mapping.
	 * @throws InvalidArgumentException If $uri is not a string.
	 */
	public function removeTemplateMapping($uri) {
		
		if (gettype($uri) !== 'string') {
			$msg = "The uri argument must be a string, '" . gettype($uri) . "' given.";
			throw new InvalidArgumentException($msg);
		}
		
		$templateMapping = &$this->getTemplateMapping();
		
		if (isset($templateMapping[$uri]) === true) {
			unset($templateMapping[$uri]);
		}
	}
	
	/**
	 * Get the current template mapping array.
	 * 
	 * @return array An array where keys are template URIs and values template URL (their location).
	 */
	protected function &getTemplateMapping() {
		return $this->templateMapping;
	}
	
	/**
	 * Execute the ResponseProcessing according to the current context.
	 * 
	 * @throws ResponseProcessingException
	 */
	public function process() {
		// @todo Figure out how to provide a way to the ResponseProcessingEngine to know the folder where to seek for templateLocation, which is a relative URI.
		$responseProcessing = $this->getComponent();
		$template = $responseProcessing->getTemplate();
		$templateLocation = $responseProcessing->getTemplateLocation();
		
		if (count($responseProcessing->getResponseRules()) > 0) {
			// Always prefer the embedded rules.
			$rules = $responseProcessing->getResponseRules();
		}
		else {
			$finalTemplateFile = '';
			
			if (empty($template) === false) {
				// try to locate the template file thanks to the given mapping.
				$mapping = $this->getTemplateMapping();
				if (isset($mapping[$template])) {
					$finalTemplateFile = $mapping[$template];
				}
			}
			
			if (empty($finalTemplateFile) === true && empty($templateLocation) === false) {
				// The template could not be resolved using the mapping.
				// Try to use template location.
				if (@is_readable($templateLocation) === true) {
					$finalTemplateFile = $templateLocation;
				}
			}
			
			if (empty($finalTemplateFile) === true) {
				$msg = "The template file could not be found: template='${template}', templateLocation='${templateLocation}'.";
				throw new ResponseProcessingException($msg, $this, ResponseProcessingException::TEMPLATE_NOT_FOUND);
			}
			
			// Open the file and retrieve the rules.
			$this->trace("loading response processing template '${finalTemplateFile}'");
			$xml = new XmlResponseProcessingDocument();
			$xml->load($finalTemplateFile);
			$rules = $xml->getResponseRules();
			$this->trace(count($rules) . " responseRule(s) extracted from the response processing template");
		}
		
		foreach ($rules as $rule) {
			$engine = new RuleEngine($rule, $this->getContext());
			$engine->process();
			$this->trace($rule->getQtiClassName() . ' executed');
		}
	}
}