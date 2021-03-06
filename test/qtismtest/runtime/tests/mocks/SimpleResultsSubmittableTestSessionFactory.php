<?php
namespace qtismtest\runtime\tests\mocks;

use qtism\data\AssessmentTest;
use qtism\runtime\tests\AssessmentItemSession;
use qtism\data\IAssessmentItem;
use qtism\runtime\tests\AbstractSessionManager;
use qtism\runtime\tests\Route;
use qtism\runtime\tests\TestResultsSubmission;

class SimpleResultsSubmittableTestSessionFactory extends AbstractSessionManager {
    
    protected function instantiateAssessmentTestSession(AssessmentTest $test, Route $route, $config = 0) {
        return new SimpleResultsSubmittableTestSession($test, $this, $route, $config);
    }
    
    protected function instantiateAssessmentItemSession(IAssessmentItem $assessmentItem, $navigationMode, $submissionMode) {
        return new AssessmentItemSession($assessmentItem, $navigationMode, $submissionMode);
    }
}
