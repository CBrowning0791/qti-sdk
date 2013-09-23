<?php

namespace qtism\runtime\rules;

use qtism\runtime\common\Utils as RuntimeUtils;
use qtism\runtime\common\OutcomeVariable;
use qtism\runtime\expressions\ExpressionEngine;
use qtism\data\rules\SetOutcomeValue;
use qtism\data\rules\Rule;
use qtism\common\enums\BaseType;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The setOutcomeValue rule sets the value of an outcome variable to the value 
 * obtained from the associated expression. An outcome variable can be updated with 
 * reference to a previously assigned value, in other words, the outcome variable 
 * being set may appear in the expression where it takes the value previously assigned 
 * to it.
 * 
 * Special care is required when using the numeric base-types because floating point 
 * values can not be assigned to integer variables and vice-versa. The truncate, 
 * round or integerToFloat operators must be used to achieve numeric type conversion.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class SetOutcomeValueProcessor extends RuleProcessor {
	
	/**
	 * Set the SetOutcomeValue object to be processed.
	 * 
	 * @param Rule $rule A SetOutcomeValue object.
	 * @throws InvalidArgumentException If $rule is not a SetOutcomeValue object.
	 */
	public function setRule(Rule $rule) {
		if ($rule instanceof SetOutcomeValue) {
			parent::setRule($rule);
		}
		else {
			$msg = "The SetOutcomeValueProcessor only accepts SetOutcomeValue objects to be processed.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the setOutcomeValue rule. 
	 * 
	 * A RuleProcessingException will be thrown if:
	 * 
	 * * The outcome variable does not exist.
	 * * The requested variable is not an OutcomeVariable.
	 * * The outcome variable's baseType does not match the baseType of the affected value.
	 * * An error occurs while processing the related expression.
	 * 
	 * @throws RuleProcessingException If one of the error described above arise.
	 */
	public function process() {
		$state = $this->getState();
		$rule = $this->getRule();
		$identifier = $rule->getIdentifier();
		$var = $state->getVariable($identifier);
		
		if (is_null($var) === true) {
			$msg = "No variable with identifier '${identifier}' to be set in the current state.";
			throw new RuleProcessingException($msg, $this, RuleProcessingException::NONEXISTENT_VARIABLE);
		}
		else if (!$var instanceof OutcomeVariable) {
			$msg = "The variable to set '${identifier}' is not an OutcomeVariable.";
			throw new RuleProcessingException($msg, $this, RuleProcessingException::WRONG_VARIABLE_TYPE);
		}
		
		// Process the expression.
		// Its result will be the value to set to the target variable.
		try {
			$expressionEngine = new ExpressionEngine($rule->getExpression(), $state);
			$val = $expressionEngine->process();
		}
		catch (ExpressionProcessingException $e) {
			$msg = "An error occured while processing the expression bound with the setOutcomeValue rule.";
			throw new RuleProcessingException($msg, $this, RuleProcessingException::RUNTIME_ERROR, $e);
		}
		
		// The variable exists, its new value is processed.
		try {
		    // A lot of people designing QTI items want to put float values
		    // in integer baseType'd variables... So let's go for type juggling!
		    if (RuntimeUtils::inferBaseType($val) === BaseType::FLOAT && $var->getBaseType() === BaseType::INTEGER) {
		        $val = intval($val);
		    }
		    else if (RuntimeUtils::inferBaseType($val) === BaseType::INTEGER && $var->getBaseType() === BaseType::FLOAT) {
		        $val = floatval($val);
		    }
		    
			$var->setValue($val);
		}
		catch (InvalidArgumentException $e) {
			// The affected value does not match the baseType of the variable $var.
			$msg = "Unable to set value ${val} to variable '${identifier}'.";
			throw new RuleProcessingException($msg, $this, RuleProcessingException::WRONG_VARIABLE_BASETYPE, $e);
		}
	}
}