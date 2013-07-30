<?php

namespace qtism\runtime\expressions\operators;

use qtism\data\expressions\operators\RoundingMode;
use qtism\data\expressions\Expression;
use qtism\data\expressions\operators\RoundTo;
use \InvalidArgumentException;

/**
 * The RoundToProcessor class aims at processing QTI Data Model RoundTo Operator objects.
 * 
 * From IMS QTI:
 * 
 * he roundTo operator takes one sub-expression which must have single cardinality 
 * and a numerical base-type. The result is a single float with the value nearest to 
 * that of the expression's value such that when converted to a decimal string it 
 * represents the expression rounded by the specified rounding method to the 
 * specified precision. If the sub-expression is NULL, then the result is NULL. If 
 * the sub-expression is INF, then the result is INF. If the sub-expression is -INF, 
 * then the result is -INF. If the argument is NaN, then the result is NULL.
 * 
 * When rounding to n significant figures, the deciding digit is the (n+1)th digit 
 * counting from the first non-zero digit from the left in the number. If the 
 * deciding digit is 5 or greater, the nth digit is increased by 1 and all digits to 
 * its right are discarded; if the deciding digit is less than 5, all digits to the 
 * right of the nth digit are discarded.
 * 
 * When rounding to n decimal places, the deciding digit is the (n+1)th digit 
 * counting to the right from the decimal point. If the deciding digit is 5 or 
 * greater, the nth digit is increased by 1 and all digits to its right are 
 * discarded; if the deciding digit is less than 5, all digits to the right of the 
 * nth digit are discarded.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RoundToProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof RoundTo) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The RoundToProcessor class only accepts RoundTo Operator objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the RoundTo operator.
	 * 
	 * An OperatorProcessingException will be thrown if:
	 * 
	 * * The given operand is not a numeric value.
	 * * The cardinality of the operand is not single.
	 * * The value of the 'figures' attribute comes from a templateVariable which does not exist or is not numeric or null.
	 * 
	 * @return null|float A single float with the value nearest to that of the expression's value or NULL if the sub-expression is NaN.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		$state = $this->getState();
		$operand = $operands[0];
			
		// If the value is null, return null.
		if ($operands->containsNull()) {
			return null;
		}
		
		if (!$operands->exclusivelySingle()) {
			$msg = "The RoundTo operator accepts 1 operand with single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		// Accept only numerical operands.
		if (!$operands->exclusivelyNumeric()) {
			$msg = "The RoundTo operand accepts 1 operand with numerical baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		// As per QTI 2.1 spec...
		if (is_nan($operand)) {
			return null;
		}
		else if (is_infinite($operand)) {
			return $operand;
		}
		
		$roundingMode = $this->getExpression()->getRoundingMode();
		$figures = $this->getExpression()->getFigures();
		
		if (is_string($figures)) {
			// try to recover the value from the state.
			$figuresIdentifier = Utils::sanitizeVariableRef($figures);
			$figures = $state[$figuresIdentifier];
			
			if (is_null($figures)) {
				$msg = "The variable '${figuresIdentifier}' used to set up the 'figures' attribute is null or nonexisting.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NONEXISTENT_VARIABLE);
			}
			else if (!is_int($figures)) {
				$msg = "The variable '${figuresIdentifier}' used to set up the 'figures' attribute is not an integer.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_VARIABLE_BASETYPE);
			}
		}
		
		if ($roundingMode === RoundingMode::SIGNIFICANT_FIGURES) {
			
			if ($figures <= 0) {
				// As per QTI 2.1 spec.
				$msg = "The 'figures' attribute must be a non-zero positive integer when mode 'significantFigures' is used, '${figures}' given.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::LOGIC_ERROR);
			}
			
			if ($operand == 0) {
				return 0.0;
			}
			
			$d = ceil(log10($operand < 0 ? -$operand : $operand));
			$power = $figures - intval($d);
			
			$magnitude = pow(10, $power);
			$shifted = round($operand * $magnitude);
			return floatval($shifted / $magnitude);
		}
		else {
			
			// As per QTI 2.1 spec.
			if ($figures < 0) {
				$msg = "The 'figures' attribute must be a integer greater than or equal to zero when mode 'decimalPlaces' is used, '${figures}' given.";
				throw new OperatorProcessingException($msg, $this);
			}
			
			return round($operand, $figures);
		}
	}
}