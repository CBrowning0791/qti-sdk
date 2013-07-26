<?php

namespace qtism\runtime\expressions\processing\operators;

use qtism\common\enums\BaseType;

use qtism\runtime\common\Container;
use qtism\data\expressions\operators\Gcd;
use qtism\data\expressions\Expression;
use \InvalidArgumentException;

/**
 * The GcdProcessor class aims at processing Gcd operators.
 * 
 * From IMS QTI:
 * 
 * The gcd operator takes 1 or more sub-expressions which all have base-type 
 * integer and may have single, multiple or ordered cardinality. The result is a 
 * single integer equal in value to the greatest common divisor (gcd) of the 
 * argument values. If all the arguments are zero, the result is 0, gcd(0,0)=0; 
 * authors should beware of this in calculations which require division by the 
 * gcd of random values. If some, but not all, of the arguments are zero, the 
 * result is the gcd of the non-zero arguments, gcd(0,n)=n if n<>0. If any of 
 * the sub-expressions is NULL, the result is NULL. If any of the sub-expressions 
 * is not a numerical value, then the result is NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class GcdProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof Gcd) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The GcdProcessor class only processes Gcd QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the Gcd operator.
	 * 
	 * @return integer The integer value equal in value to the greatest common divisor of the sub-expressions. If any of the sub-expressions is NULL, the result is NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull() === true) {
			return null;
		}
		
		if ($operands->anythingButRecord() === false) {
			$msg = "The Gcd operator only accepts operands with a cardinality of single, multiple or ordered.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyInteger() === false) {
			$msg = "The Gcd operator only accepts operands with an integer baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		// Make a flat collection first.
		$flatCollection = new OperandsCollection();
		$zeroCount = 0;
		$valueCount = 0;
		foreach ($operands as $operand) {
			if (is_scalar($operand) === true) {
				
				$valueCount++;
				
				if ($operand !== 0) {
					$flatCollection[] = $operand;
				}
				else {
					$zeroCount++;
				}
			}
			else if ($operand->contains(null)) {
				// Container with at least one null value inside.
				// -> If any of the sub-expressions is null or not numeric, returns null.
				return null;
			}
			else {
				// Container with no null values.
				foreach ($operand as $o) {
					$valueCount++;
					
					if ($o !== 0) {
						$flatCollection[] = $o;
					}
					else {
						$zeroCount++;
					}
				}
			}
		}
		
		if ($zeroCount === $valueCount) {
			// All arguments of gcd() are 0.
			return 0;
		}
		else {
			$g = $flatCollection[0];
			$loopLimit = count($flatCollection) - 1;
			$i = 0;
			
			while ($i < $loopLimit) {
				$g = Utils::gcd($g, $flatCollection[$i + 1]);
				$i++;
			}
			
			return $g;
		}
	}
}