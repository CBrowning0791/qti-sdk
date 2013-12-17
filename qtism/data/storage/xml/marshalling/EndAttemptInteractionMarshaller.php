<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *   
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 * @author Jérôme Bogaerts, <jerome@taotesting.com>
 * @license GPLv2
 * @package 
 */


namespace qtism\data\storage\xml\marshalling;

use qtism\data\content\interactions\EndAttemptInteraction;
use qtism\data\QtiComponent;
use \InvalidArgumentException;
use \DOMElement;

/**
 * Marshalling/Unmarshalling implementation for EndAttemptInteraction.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EndAttemptInteractionMarshaller extends Marshaller {
	
	/**
	 * Marshall an EndAttemptInteraction object into a DOMElement object.
	 * 
	 * @param QtiComponent $component An EndAttemptInteraction object.
	 * @return DOMElement The according DOMElement object.
	 * @throws MarshallingException
	 */
	protected function marshall(QtiComponent $component) {
        $element = self::getDOMCradle()->createElement('endAttemptInteraction');
        self::fillElement($element, $component);
        self::setDOMElementAttribute($element, 'responseIdentifier', $component->getResponseIdentifier());
        self::setDOMElementAttribute($element, 'title', $component->getTitle());
        
        if ($component->hasXmlBase() === true) {
            self::setXmlBase($element, $component->getXmlBase());
        }
        
        return $element;
	}
	
	/**
	 * Unmarshall a DOMElement object corresponding to an endAttemptInteraction element.
	 * 
	 * @param DOMElement $element A DOMElement object.
	 * @return QtiComponent An EndAttemptInteraction object.
	 * @throws UnmarshallingException
	 */
	protected function unmarshall(DOMElement $element) {
	    
	    if (($responseIdentifier = self::getDOMElementAttributeAs($element, 'responseIdentifier')) !== null) {
            
	        if (($title = self::getDOMElementAttributeAs($element, 'title')) !== null) {
	            $component = new EndAttemptInteraction($responseIdentifier, $title);
	            
	            if (($xmlBase = self::getXmlBase($element)) !== false) {
	                $component->setXmlBase($xmlBase);
	            }
	            
	            self::fillBodyElement($component, $element);
	            return $component;
	        }
	        else {
	            $msg = "The mandatory 'title' attribute is missing from the 'endAttemptInteraction' element.";
	            throw new UnmarshallingException($msg, $element);
	        }
        }
        else {
            $msg = "The mandatory 'responseIdentifier' attribute is missing from the 'endAttemptInteraction' element.";
            throw new UnmarshallingException($msg, $element);
        }
	}
	
	public function getExpectedQtiClassName() {
		return 'endAttemptInteraction';
	}
}
