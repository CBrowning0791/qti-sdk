<?php
namespace qtismtest\data\storage\xml\marshalling;

use qtismtest\QtiSmTestCase;
use qtism\data\state\AssociationValidityConstraint;
use qtism\data\storage\xml\marshalling\CompactMarshallerFactory;
use \DOMDocument;

class AssociationValidityConstraintMarshallerTest extends QtiSmTestCase {
    
    public function testUnmarshallSimple() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint identifier="IDENTIFIER" minConstraint="0" maxConstraint="1"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        $component = $factory->createMarshaller($element)->unmarshall($element);
        
        $this->assertInstanceOf('qtism\\data\\state\\AssociationValidityConstraint', $component);
        $this->assertEquals('IDENTIFIER', $component->getIdentifier());
        $this->assertEquals(0, $component->getMinConstraint());
        $this->assertEquals(1, $component->getMaxConstraint());
    }
    
    
    public function testUnmarshallNoIdentifier() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint minConstraint="0" maxConstraint="1"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException',
            "The mandatory 'identifier' attribute is missing from element 'associationValididtyConstraint'."
        );
        $component = $factory->createMarshaller($element)->unmarshall($element);
    }
    
    public function testUnmarshallNoMinConstraint() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint identifier="IDENTIFIER" maxConstraint="1"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException',
            "The mandatory 'minConstraint' attribute is missing from element 'associationValididtyConstraint'."
        );
        $component = $factory->createMarshaller($element)->unmarshall($element);
    }
    
    public function testUnmarshallNoMaxConstraint() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint identifier="IDENTIFIER" minConstraint="0"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException',
            "The mandatory 'maxConstraint' attribute is missing from element 'associationValididtyConstraint'."
        );
        $component = $factory->createMarshaller($element)->unmarshall($element);
    }
    
    public function testUnmarshallInvalidMaxConstraintOne() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint identifier="RESPONSE" minConstraint="0" maxConstraint="-2"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException',
            "An error occured while unmarshalling an 'associationValidityConstraint' element. See chained exceptions for more information."
        );
        $component = $factory->createMarshaller($element)->unmarshall($element);
    }
    
    public function testUnmarshallInvalidMaxConstraintTwo() {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML('<associationValidityConstraint identifier="IDENTIFIER" minConstraint="2" maxConstraint="1"/>');
        $element = $dom->documentElement;
        $factory = new CompactMarshallerFactory();
        
        $this->setExpectedException(
            '\\qtism\\data\\storage\\xml\\marshalling\\UnmarshallingException',
            "An error occured while unmarshalling an 'associationValidityConstraint' element. See chained exceptions for more information."
        );
        $component = $factory->createMarshaller($element)->unmarshall($element);
    }
    
    public function testMarshallSimple() {
        $component = new AssociationValidityConstraint('IDENTIFIER', 0, 1);
        $factory = new CompactMarshallerFactory();
        
        $element = $factory->createMarshaller($component)->marshall($component);
        $this->assertEquals('IDENTIFIER', $element->getAttribute('identifier'));
        $this->assertEquals('0', $element->getAttribute('minConstraint'));
        $this->assertEquals('1', $element->getAttribute('maxConstraint'));
    }
}
