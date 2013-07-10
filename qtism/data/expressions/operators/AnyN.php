<?php

namespace qtism\data\expressions\operators;

use qtism\common\enums\Cardinality;
use qtism\common\utils\Format;
use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The anyN operator takes one or more sub-expressions each with a base-type of boolean 
 * and single cardinality. The result is a single boolean which is true if at least min 
 * of the sub-expressions are true and at most max of the sub-expressions are true.
 * If more than n - min sub-expressions are false (where n is the total number of 
 * sub-expressions) or more than max sub-expressions are true then the result is false.
 * If one or more sub-expressions are NULL then it is possible that neither of these 
 * conditions is satisfied, in which case the operator results in NULL. 
 * For example, if min is 3 and max is 4 and the sub-expressions have 
 * values {true,true,false,NULL} then the operator results in NULL 
 * whereas {true,false,false,NULL} results in false and {true,true,true,NULL} 
 * results in true. The result NULL indicates that the correct value for the 
 * operator cannot be determined.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class AnyN extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * The minimum number of sub-expressions that must be true.
	 * 
	 * @var integer|string
	 */
	private $min;
	
	/**
	 * From IMS QTI:
	 * 
	 * The maximum number of sub-expressions that may be true.
	 * 
	 * @var string|integer
	 */
	private $max;
	
	/**
	 * Create a new instance of AnyN.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param string|integer $min An integer or a variable reference.
	 * @param string|integer $max An integer or a variable reference.
	 */
	public function __construct(ExpressionCollection $expressions, $min, $max) {
		parent::__construct($expressions, 1, -1, array(Cardinality::SINGLE), array(OperatorBaseType::BOOLEAN));
		$this->setMin($min);
		$this->setMax($max);
	}
	
	/**
	 * Set the min attribute.
	 * 
	 * @param string|integer $min An integer or a variable reference.
	 * @throws InvalidArgumentException If $min is not an integer nor a variable reference.
	 */
	public function setMin($min) {
		if (is_int($min) || (is_string($min) && Format::isVariableRef($min))) {
			$this->min = $min;
		}
		else {
			$msg = "The min attribute must be an integer or a variable reference, '" . $min . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the min attribute.
	 * 
	 * @return string|integer An integer or a variable reference.
	 */
	public function getMin() {
		return $this->min;
	}
	
	/**
	 * Set the max attribute.
	 * 
	 * @param string|integer $max An integer or a variable reference.
	 * @throws InvalidArgumentException If $max is not an integer nor a variable reference.
	 */
	public function setMax($max) {
		if (is_int($max) || (is_string($max) && Format::isVariableRef($max))) {
			$this->max = $max;
		}
		else {
			$msg = "The max attribute must be an integer or a variable reference, '" . $max . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the max attribute.
	 * 
	 * @return string|integer An integer or a variable reference.
	 */
	public function getMax() {
		return $this->max;
	}
	
	public function getQTIClassName() {
		return 'anyN';
	}
}