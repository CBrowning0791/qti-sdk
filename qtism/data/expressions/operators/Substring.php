<?php

namespace qtism\data\expressions\operators;

use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The substring operator takes two sub-expressions which must both have an effective
 * base-type of string and single cardinality. The result is a single boolean with a
 * value of true if the first expression is a substring of the second expression and
 * false if it isn't. If either sub-expression is NULL then the result of the operator
 * is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Substring extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * Used to control whether or not the substring is matched case sensitively.
	 * If true then the match is case sensitive and, for example, "Hell" is not 
	 * a substring of "Shell". If false then the match is not case sensitive and "Hell" 
	 * is a substring of "Shell".
	 * 
	 * @var boolean
	 */
	private $caseSensitive = true;
	
	/**
	 * Create a new Substring.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param boolean $caseSensitive A boolean value.
	 * @throws InvalidArgumentException If $caseSensitive is not a boolean or if the count of $expressions is not correct.
	 */
	public function __construct(ExpressionCollection $expressions, $caseSensitive = true) {
		parent::__construct($expressions, 2, 2, array(OperatorCardinality::SINGLE), array(OperatorBaseType::STRING));
		$this->setCaseSensitive($caseSensitive);
	}
	
	/**
	 * Set the caseSensitive attribute.
	 * 
	 * @param boolean $caseSensitive A boolean value.
	 * @throws InvalidArgumentException If $caseSensitive is not a boolean value.
	 */
	public function setCaseSensitive($caseSensitive) {
		if (is_bool($caseSensitive)) {
			$this->caseSensitive = $caseSensitive;
		}
		else {
			$msg = "The caseSensitive argument must be a boolean value, '" . gettype($caseSensitive) . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Wether or not the operator is case sensitive.
	 * 
	 * @return boolean
	 */
	public function isCaseSensitive() {
		return $this->caseSensitive;
	}
	
	public function getQtiClassName() {
		return 'substring';
	}
}