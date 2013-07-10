<?php

namespace qtism\data\expressions\operators;

use qtism\common\datatypes\Shape;
use qtism\common\datatypes\Coords;
use qtism\data\expressions\ExpressionCollection;
use \InvalidArgumentException;

/**
 * From IMS QTI:
 * 
 * The inside operator takes a single sub-expression which must have a baseType of point.
 * The result is a single boolean with a value of true if the given point is inside the
 * area defined by shape and coords. If the sub-expression is a container the result is
 * true if any of the points are inside the area. If either sub-expression is NULL then
 * the operator results in NULL.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class Inside extends Operator {
	
	/**
	 * From IMS QTI:
	 * 
	 * The shape of the area.
	 * 
	 * @var int
	 */
	private $shape;
	
	/**
	 * From IMS QTI:
	 * 
	 * The size and position of the area, interpreted in conjunction with the shape.
	 * 
	 * @var Coords
	 */
	private $coords;
	
	/**
	 * Create a new Inside object.
	 * 
	 * @param ExpressionCollection $expressions A collection of Expression objects.
	 * @param int $shape A value from the Shape enumeration
	 * @param Coords $coords A Coords object as the size and position of the area, interpreted in conjunction with $shape.
	 * @throws InvalidArgumentException If the $expressions count exceeds 1 or if $shape is not a value from the Shape enumeration.
	 */
	public function __construct(ExpressionCollection $expressions, $shape, Coords $coords) {
		$this->setExpressions($expressions, 1, 1, array(OperatorCardinality::SINGLE, OperatorCardinality::MULTIPLE, OperatorCardinality::ORDERED), array(OperatorBaseType::POINT));
		$this->setShape($shape);
		$this->setCoords($coords);
	}
	
	/**
	 * Set the shape.
	 * 
	 * @param int $shape A value from the Shape enumeration.
	 * @throws InvalidArgumentException If $shape is not a value from the Shape enumeration.
	 */
	public function setShape($shape) {
		if (in_array($shape, Shape::asArray())) {
			$this->shape = $shape;
		}
		else {
			$msg = "The shape argument must be a value from the Shape enumeration, '" . $shape . "' given.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Get the shape.
	 * 
	 * @return int A value from the Shape enumeration.
	 */
	public function getShape() {
		return $this->shape;
	}
	
	/**
	 * Set the coordinates.
	 * 
	 * @param Coords $coords A Coords object.
	 */
	public function setCoords(Coords $coords) {
		$this->coords = $coords;
	}
	
	/**
	 * Get the coordinates
	 * 
	 * @return Coords A Coords object.
	 */
	public function getCoords() {
		return $this->coords;
	}
	
	public function getQTIClassName() {
		return 'inside';
	}
}