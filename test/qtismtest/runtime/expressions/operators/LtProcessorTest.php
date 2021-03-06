<?php
namespace qtismtest\runtime\expressions\operators;

use qtismtest\QtiSmTestCase;
use qtism\common\datatypes\QtiBoolean;
use qtism\common\datatypes\QtiInteger;
use qtism\common\datatypes\QtiFloat;
use qtism\runtime\common\RecordContainer;
use qtism\common\datatypes\QtiPoint;
use qtism\runtime\expressions\operators\LtProcessor;
use qtism\runtime\expressions\operators\OperandsCollection;

class LtProcessorTest extends QtiSmTestCase {
	
	public function testLt() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new QtiFloat(0.5);
		$operands[] = new QtiInteger(1);
		$processor = new LtProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
		$this->assertTrue($result->getValue());
		
		$operands->reset();
		$operands[] = new QtiInteger(1);
		$operands[] = new QtiFloat(0.5);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
		$this->assertFalse($result->getValue());
		
		$operands->reset();
		$operands[] = new QtiInteger(1);
		$operands[] = new QtiInteger(1);
		$result = $processor->process();
		$this->assertInstanceOf('qtism\\common\\datatypes\\QtiBoolean', $result);
		$this->assertFalse($result->getValue());
	}
	
	public function testNull() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new QtiInteger(1);
		$operands[] = null;
		$processor = new LtProcessor($expression, $operands);
		$result = $processor->process();
		$this->assertSame(null, $result);
	}
	
	public function testWrongBaseTypeOne() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new QtiInteger(1);
		$operands[] = new QtiBoolean(true);
		$processor = new LtProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongBaseTypeTwo() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new QtiPoint(1, 2);
		$operands[] = new QtiInteger(2);
		$processor = new LtProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testWrongCardinality() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$operands[] = new RecordContainer(array('A' => new QtiInteger(1)));
		$operands[] = new QtiInteger(2);
		$processor = new LtProcessor($expression, $operands);
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$result = $processor->process();
	}
	
	public function testNotEnoughOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection();
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new LtProcessor($expression, $operands);
	}
	
	public function testTooMuchOperands() {
		$expression = $this->createFakeExpression();
		$operands = new OperandsCollection(array(new QtiInteger(1), new QtiInteger(2), new QtiInteger(3)));
		$this->setExpectedException('qtism\\runtime\\expressions\\ExpressionProcessingException');
		$processor = new LtProcessor($expression, $operands);
	}
	
	public function createFakeExpression() {
		return $this->createComponentFromXml('
			<lt>
				<baseValue baseType="float">9.9</baseValue>
				<baseValue baseType="integer">10</baseValue>
			</lt>
		');
	}
}