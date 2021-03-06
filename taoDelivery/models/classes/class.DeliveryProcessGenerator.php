<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * TAO - taoDelivery/models/classes/class.DeliveryProcessGenerator.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 26.10.2012, 11:35:24 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include wfAuthoring_models_classes_ProcessCloner
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('wfAuthoring/models/classes/class.ProcessCloner.php');

/* user defined includes */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-includes begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-includes end

/* user defined constants */
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-constants begin
// section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007177-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoDelivery
 * @subpackage models_classes
 */
class taoDelivery_models_classes_DeliveryProcessGenerator
    extends wfAuthoring_models_classes_ProcessCloner
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute processError
     *
     * @access protected
     * @var array
     */
    protected $processError = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function __construct()
    {
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000717D begin
		parent::__construct();
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:000000000000717D end
    }

    /**
     * Short description of method generateDeliveryProcess
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource delivery
     * @return core_kernel_classes_Resource
     */
    public function generateDeliveryProcess( core_kernel_classes_Resource $delivery)
    {
        $returnValue = null;

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007180 begin
		$failed = false;
		
		
		$this->processError = array('tests'=>array());
		$this->initCloningVariables();
		// $this->setCloneLabel("__Clone1");
		
		$process = $delivery->getUniquePropertyValue(new core_kernel_classes_Property(TAO_DELIVERY_DELIVERYCONTENT));
		
		//check delivery process:
		$deliveryProcessChecker = new taoDelivery_models_classes_DeliveryProcessChecker($process);
		if(!$deliveryProcessChecker->check()){
			$this->processError['delivery'] = array(
				'resource' => $delivery,
				'initialActivity' => (bool) count($deliveryProcessChecker->getInitialActivities()),
				'isolatedConnectors' => $deliveryProcessChecker->getIsolatedConnectors()
			);
			return $returnValue;
		}
		
		$deliveryProcess = null;
		$deliveryProcess = $this->cloneWfResource(
			$process, 
			new core_kernel_classes_Class(CLASS_PROCESS), 
			array(PROPERTY_PROCESS_ACTIVITIES, PROPERTY_PROCESS_DIAGRAMDATA),
			$delivery->getLabel()
		);
		
		if(is_null($deliveryProcess)){
			throw new common_exception_Error('Delivery Process '.$process->getUri().' could not be cloned');
		}
		$this->clonedProcess = $deliveryProcess;
			
		//get all activity processes and clone them:
		$activities = $this->authoringService->getActivitiesByProcess($process);
		
		$deliveryAuthoringService = taoDelivery_models_classes_DeliveryAuthoringService::singleton();
		
		$toLink = array();
		foreach($activities as $activityUri => $activity){
			
			$testProcess = $deliveryAuthoringService->getTestProcessFromActivity($activity);
			
			if(!is_null($testProcess)){
				//validate the test process:
				$processChecker = new taoDelivery_models_classes_DeliveryProcessChecker($testProcess);
				
				if($processChecker->check()){
					
					//clone the process segment:
					$testInterfaces = $this->cloneProcessSegment($testProcess, false);
					// print_r($testInterfaces);
					
					if(!empty($testInterfaces['in']) && !empty($testInterfaces['out'])){
						$inActivity = $testInterfaces['in'];
						$firstout = current($testInterfaces['out']);
						$this->addClonedActivity($inActivity, $activity, $firstout);
						common_Logger::i('Cloned T '.$activity->getUri().' to '.$inActivity->getUri().'=>'.$firstout->getUri());
						
						$toLink[] = $activity;
					}else{
						throw new Exception("the process segment of the test process {$testProcess->getUri()} cannot be cloned");
					}
				}else{
					
					//log error:
					$failed = true;
					
					$testClass = new core_kernel_classes_Class(TAO_TEST_CLASS);
					$testArray = $testClass->searchInstances(array(TEST_TESTCONTENT_PROP => $testProcess->uriResource), array('like' => false, 'recursive' => 1000));
					if(count($testArray)){
						$test = array_shift($testArray);
						$this->processError['tests'][$test->uriResource] = array(
							'resource' => $test,
							'initialActivity' => (bool) count($processChecker->getInitialActivities()),
							'isolatedConnectors' => $processChecker->getIsolatedConnectors()
						);
					}else{
						throw new Exception('no test found for the related test process');
					}
					
				}
			}else{
				$activityClone = $this->cloneActivity($activity);
				if(is_null($activityClone)){
					throw new common_Exception("the activity '{$activity->getLabel()}'({$activity->uriResource}) cannot be cloned");
				}else{
					$this->addClonedActivity($activityClone, $activity);
				}
			}
		}
	
		if($failed){
			common_Logger::w('Something failed during ');
			//cancel everything
			$this->revertCloning();
			$this->authoringService->deleteProcess($deliveryProcess);
			$deliveryProcess = null;
			
		}else{
			//add all cloned activities to the cloned delivery process:
			foreach($this->getClonedActivities() as $activityClone){
				$deliveryProcess->setPropertyValue(new core_kernel_classes_Property(PROPERTY_PROCESS_ACTIVITIES), $activityClone->uriResource);
			}
			
			//reloop for connectors this time:
			foreach($activities as $activityUri => $activity){
				$this->currentActivity = $activity;
				$connectors = $this->authoringService->getConnectorsByActivity($activity, array('next'));
				//$this->linkClonedStep($this->getClonedActivity($activity, 'out'), $activity);
				foreach($connectors['next'] as $connector){
					$clone = $this->cloneConnector($connector);
					$this->addClonedConnector($connector, $clone);
					$toLink[] = $connector;
				}
			}
			
			foreach($toLink as $step){
				common_Logger::d('relinking '.$step->getLabel().'('.$step->getUri().')');
				$this->linkClonedStep($step);
			}
			
			//set the valid delivery process as the return value:
			$returnValue = $deliveryProcess;
		}
			
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007180 end

        return $returnValue;
    }

    /**
     * Short description of method getErrors
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getErrors()
    {
        $returnValue = array();

        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007183 begin
		$returnValue = $this->processError;
        // section 10-13-1-39--56440278:12d4c05ae3c:-8000:0000000000007183 end

        return (array) $returnValue;
    }

} /* end of class taoDelivery_models_classes_DeliveryProcessGenerator */

?>