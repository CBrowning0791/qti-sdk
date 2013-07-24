<?php

namespace qtism\runtime\expressions\processing;

use qtism\data\expressions\operators\Power;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The PowerProcessor class aims at processing PowerValue expressions.
 * 
 * From IMS QTI:
 * 
 * The power operator takes 2 sub-expression which both have single cardinality and 
 * numerical base-types. The result is a single float that corresponds to the first 
 * expression raised to the power of the second. If either or the sub-expressions is 
 * NULL then the operator results in NULL.
 * 
 * If the resulting value is outside the value set defined by float (not including 
 * positive and negative infinity) then the operator shall result in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class PowerProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Power) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The PowerProcessor class only processes Power QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Power operator.
	 * 
	 * @return float|null A float value that corresponds to the first expression raised to the power of the second or NULL if the either sub-expression is NULL.
	 * @throws ExpressionProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The Power operator only accepts operands with a single cardinality.";
			throw new ExpressionProcessingException($msg, $this);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The Power operator only accepts operands with a baseType of integer or float.";
			throw new ExpressionProcessingException($msg, $this);
		}
		
		$operand1 = $operands[0];
		$operand2 = $operands[1];
		$raised = pow($operand1, $operand2);
		
		if (is_nan($raised)) {
			return null;
		}
		
		// If the first operand was not 0 but the result is 0, it means
		// we are subject to a lower overflow.
		if ($operand1 != 0 && $raised == 0) {
			return null;
		}
		
		// If the first and the second operands are not infinite but the result is infinite
		// it means we are subject to an upper overflow.
		if (!is_infinite($operand1) && !is_infinite($operand2) && is_infinite($raised)) {
			return null;
		}
		
		// pow() returns integers as much as it can, so we must cast.
		// If the casted value cannot be contained in a float, we are
		// subject to an overflow/underflow.
		$floatval = floatval($raised);
		if ($raised != 0 && $floatval == 0) {
			// underflow
			return null;
		}
		else if (!is_infinite($raised) && is_infinite($floatval)) {
			// overflow
			return null;
		}
		else {
			return $floatval;
		}
	}
}