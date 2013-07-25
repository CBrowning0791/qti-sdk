<?php

namespace qtism\runtime\expressions\processing;

use qtism\data\expressions\operators\ToleranceMode;
use qtism\data\expressions\operators\Equal;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The EqualProcessor class aims at processing Equal operators.
 * 
 * From IMS QTI:
 * 
 * The equal operator takes two sub-expressions which must both have single 
 * cardinality and have a numerical base-type. The result is a single boolean 
 * with a value of true if the two expressions are numerically equal and false 
 * if they are not. If either sub-expression is NULL then the operator results 
 * in NULL.
 * 
 * When comparing two floating point numbers for equality it is often desirable 
 * to have a tolerance to ensure that spurious errors in scoring are not 
 * introduced by rounding errors. The tolerance mode determines whether 
 * the comparison is done exactly, using an absolute range or a relative range.
 * 
 * If the tolerance mode is absolute or relative then the tolerance must be specified.
 * The tolerance consists of two positive numbers, t0 and t1, that define the lower 
 * and upper bounds. If only one value is given it is used for both.
 *
 * In absolute mode the result of the comparison is true if the value of the 
 * second expression, y is within the following range defined by the first value, x.
 *
 * x-t0,x+t1 
 *
 * In relative mode, t0 and t1 are treated as percentages and the following 
 * range is used instead.
 * 
 * x*(1-t0/100),x*(1+t1/100)
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Equal) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The EqualProcessor class only processes Equal QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Equal operator.
	 * 
	 * @return boolean|null Whether the two expressions are numerically equal and false if they are not or NULL if either sub-expression is NULL.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Equal operator only accepts operands with a single cardinality.";
			throw new ExpressionProcessingException($msg, $this);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Equal operator only accepts operands with an integer or float baseType";
			throw new ExpressionProcessingException($msg, $this);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		$expression = $this->getExpression();
		
		if ($expression->getToleranceMode() === ToleranceMode::EXACT) {
			return $operand1 == $operand2;
		}
		else {
			$tolerance = $expression->getTolerance();
			
			if (is_string($tolerance[0])) {
				$strTolerance = $tolerance;
				$tolerance = array();
				
				// variableRef to handle.
				$state = $this->getState();
				$tolerance0Name = Utils::sanitizeVariableRef($strTolerance[0]);
				$varValue = $state[$tolerance0Name];
				
				if (is_null($varValue)) {
					$msg = "The variable with name '${tolerance0Name}' could not be resolved.";
					throw new ExpressionProcessingException($msg, $this);
				}
				else if (!is_float($varValue)) {
					$msg = "The variable with name '${tolerance0Name}' is not a float.";
					throw new ExpressionProcessingException($msg, $this);
				}
				
				$tolerance[] = $varValue;
				
				if (isset($strTolerance[1]) && is_string($strTolerance[1])) {
					// A second variableRef to handle.
					$tolerance1Name = Utils::sanitizeVariableRef($strTolerance[1]);
					
					if (($varValue = $state[$tolerance1Name]) !== null && is_float($varValue)) {
						$tolerance[] = $varValue;
					}
				}
			}
			
			if ($expression->getToleranceMode() === ToleranceMode::ABSOLUTE) {
				
				$t0 = $operand1 - $tolerance[0];
				$t1 = $operand1 + ((isset($tolerance[1])) ? $tolerance[1] : $tolerance[0]);
					
				$moreThanLower = ($expression->doesIncludeLowerBound()) ? $operand2 >= $t0 : $operand2 > $t0;
				$lessThanUpper = ($expression->doesIncludeUpperBound()) ? $operand2 <= $t1 : $operand2 < $t1;
					
				return $moreThanLower && $lessThanUpper;
			}
			else {
				// Tolerance mode RELATIVE
				$tolerance = $expression->getTolerance();
				$t0 = $operand1 * (1 - $tolerance[0] / 100);
				$t1 = $operand1 * (1 + ((isset($tolerance[1])) ? $tolerance[1] : $tolerance[0]) / 100);
					
				$moreThanLower = ($expression->doesIncludeLowerBound()) ? $operand2 >= $t0 : $operand2 > $t0;
				$lessThanUpper = ($expression->doesIncludeUpperBound()) ? $operand2 <= $t1 : $operand2 < $t1;
					
				return $moreThanLower && $lessThanUpper;
			}
		}
	}
}