<?php

namespace qtism\runtime\storage\binary;

use \Exception;

class QtiBinaryStreamAccessException extends BinaryStreamAccessException {
    
    /**
     * An error occured while reading/writing a Variable.
     * 
     * @var integer
     */
    const VARIABLE = 10;
    
    /**
     * An error occured while reading/writing a Record Field.
     * 
     * @var integer
     */
    const RECORDFIELD = 11;
    
    /**
     * An error occured while reading/writing a QTI identifier.
     * 
     * @var integer
     */
    const IDENTIFIER = 12;
    
    /**
     * An error occured while reading/writing a QTI point.
     * 
     * @var integer
     */
    const POINT = 13;
    
    /**
     * An error occured while reading/writing a QTI pair.
     * 
     * @var integer
     */
    const PAIR = 14;
    
    /**
     * An error occured while reading/writing a QTI directedPair.
     * 
     * @var integer
     */
    const DIRECTEDPAIR = 15;
    
    /**
     * An error occured while reading/writing a QTI duration.
     * 
     * @var integer
     */
    const DURATION = 16;
    
    /**
     * An error occured while reading/writing a URI.
     * 
     * @var integer
     */
    const URI = 17;
    
    /**
     * An error occured while reading/writing File's binary data.
     * 
     * @var integer
     */
    const FILE = 18;
    
    /**
     * An error occured while reading/writing an intOrIdentifier.
     * 
     * @var integer
     */
    const INTORIDENTIFIER = 19;
    
    /**
     * An error occured while reading/writing an assessment item session.
     * 
     * @var integer
     */
    const ITEM_SESSION = 20;
    
    /**
     * An error occured while reading/writing a route item.
     * 
     * @var integer
     */
    const ROUTE_ITEM = 21;
    
    /**
     * An error occured while reading/writing pending responses.
     * 
     * @var integer
     */
    const PENDING_RESPONSES = 22;
    
    /**
     * Create a new QtiBinaryStreamAccessException object.
     *
     * @param string $message A human-readable message.
     * @param BinaryStreamAccess $source The BinaryStreamAccess object that caused the error.
     * @param integer $code An exception code. See class constants.
     * @param Exception $previous An optional previously thrown exception.
     */
    public function __construct($message, BinaryStreamAccess $source, $code = 0, Exception $previous = null) {
        parent::__construct($message, $source, $code, $previous);
    }
}