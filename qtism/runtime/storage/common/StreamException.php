<?php

namespace qtism\runtime\storage\common;

use \Exception;

/**
 * The StreamException class represents the exception that might occur while
 * dealing with data streams.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
abstract class StreamException extends Exception {
    
    /**
     * Unknown error.
     * 
     * @var integer
     */
    const UNKNOWN = 0;
    
    /**
     * Error while opening a data stream.
     * 
     * @var integer
     */
    const OPEN = 1;
    
    /**
     * Error while writing a data stream.
     * 
     * @var integer
     */
    const WRITE = 2;
    
    /**
     * Error while closing a data stream.
     * 
     * @var integer
     */
    const CLOSE = 3;
    
    /**
     * Error while reading a data stream.
     * 
     * @var integer
     */
    const READ = 4;
    
    /**
     * Error while reading, writing, eof, or closing
     * but the stream is not open.
     */
    const NOT_OPEN = 5;
    
    /**
     * Error while opening the stream but it is already opened.
     * 
     * @var integer
     */
    const ALREADY_OPEN = 6;
    
    /**
     * Error during a rewind call.
     * 
     * @var integer
     */
    const REWIND = 7;
    
    /**
     * The IStream object where in the error occured.
     * 
     * @var IStream
     */
    private $source;
    
    /**
     * Create a new StreamException.
     * 
     * @param string $message The human-readable message describing the error.
     * @param IStream $source The IStream object where in the error occured.
     * @param integer $code A code describing the error.
     * @param Exception $previous An optional previous exception.
     */
    public function __construct($message, IStream $source, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->setSource($source);
    }
    
    /**
     * Get the IStream object where the error occured.
     * 
     * @return IStream An IStream object.
     */
    public function getSource() {
        return $this->source;
    }
    
    /**
     * Set the IStream object where the error occured.
     * 
     * @param IStream $source An IStream object.
     */
    protected function setSource(IStream $source) {
        $this->source = $source;
    }
}