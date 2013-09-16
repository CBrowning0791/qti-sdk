<?php

namespace qtism\runtime\common;

use qtism\common\enums\Cardinality;
use qtism\data\state\ValueCollection;
use qtism\common\utils\Arrays;
use qtism\common\enums\BaseType;
use \InvalidArgumentException;
use \RuntimeException;

/**
 * Implementation of the qti:record cardinality. It behaves as an associative
 * array. There is no information in the QTI standard about how the equality of 
 * two records can be established. In this implementation, it is implemented as
 * if it was a bag, and the keys are not taken into account.
 * 
 * From IMS QTI:
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class RecordContainer extends Container {
	
	/**
	 * Create a new RecordContainer object.
	 * 
	 * @param array $array An associative array.
	 * @throws InvalidArgumentException If the given $array is not associative.
	 */
	public function __construct(array $array = array()) {
		if (Arrays::isAssoc($array)) {
			$dataPlaceHolder = &$this->getDataPlaceHolder();
		
			foreach ($array as $k => $v) {
				$this->checkType($v);
				$dataPlaceHolder[$k] = $v;
			}
		
			reset($dataPlaceHolder);
		}
		else {
			$msg = "The array argument must be an associative array.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	public function getCardinality() {
		return Cardinality::RECORD;
	}
	
	/**
	 * Overloading of offsetSet that makes sure that the $offset
	 * is a string.
	 *
	 * @param string $offset A string offset.
	 * @param mixed $value A value.
	 *
	 * @throws RuntimeException If $offset is not a string.
	 */
	public function offsetSet($offset, $value) {
		if (gettype($offset) === 'string') {
			$this->checkType($value);
			$placeholder = &$this->getDataPlaceHolder();
			$placeholder[$offset] = $value;
		}
		else {
			$msg = "The offset of a value in a RecordContainer must be a string.";
			throw new RuntimeException($msg);
		}
	}
	
	/**
	 * Create a RecordContainer object from a Data Model ValueCollection object.
	 *
	 * @param ValueCollection $valueCollection A collection of qtism\data\state\Value objects.
	 * @return RecordContainer A Container object populated with the values found in $valueCollection.
	 * @throws InvalidArgumentException If a value from $valueCollection is not compliant with the QTI Runtime Model or the container type or if a value has no fieldIdentifier.
	 */
	public static function createFromDataModel(ValueCollection $valueCollection, $baseType = BaseType::INTEGER) {
		$container = new static();
		foreach ($valueCollection as $value) {
			$fieldIdentifier = $value->getFieldIdentifier();
			
			if (!empty($fieldIdentifier)) {
				$container[$value->getFieldIdentifier()] = $value->getValue();
			}
			else {
				$msg = "Cannot include qtism\\data\\state\\Value '" . $value->getValue() . "' in the RecordContainer ";
				$msg .= "because it has no fieldIdentifier specified.";
				throw new InvalidArgumentException($msg);
			}
		}
		return $container;
	}
	
	protected function getToStringBounds() {
		return array('{', '}');
	}
}