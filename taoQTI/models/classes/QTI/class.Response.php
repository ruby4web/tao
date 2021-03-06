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
 * Copyright (c) 2013 (original work) Open Assessment Techonologies SA (under the project TAO-PRODUCT);
 *               
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * A response is on object associated to an interactino containing which are the
 * response into the interaction choices and the score regarding the answers
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/class.Data.php');

/**
 * The QTI's interactions are the way the user interact with the system. The
 * will be rendered into widgets to enable the user to answer to the item.
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 */
require_once('taoQTI/models/classes/QTI/class.Interaction.php');

/* user defined includes */
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-includes begin
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-includes end

/* user defined constants */
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-constants begin
// section 127-0-1-1--4be859a6:12a33452171:-8000:000000000000241D-constants end

/**
 * A response is on object associated to an interactino containing which are the
 * response into the interaction choices and the score regarding the answers
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10074
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Response
    extends taoQTI_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute correctResponses
     *
     * @access protected
     * @var array
     */
    protected $correctResponses = array();

    /**
     * Short description of attribute mapping
     *
     * @access protected
     * @var array
     */
    protected $mapping = array();

    /**
     * Short description of attribute areaMapping
     *
     * @access protected
     * @var array
     */
    protected $areaMapping = array();

    /**
     * Short description of attribute mappingDefaultValue
     *
     * @access protected
     * @var double
     */
    protected $mappingDefaultValue = 0.0;

    /**
     * Short description of attribute howMatch
     *
     * @access protected
     * @var String
     */
    protected $howMatch = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getCorrectResponses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getCorrectResponses()
    {
        $returnValue = array();

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002407 begin
        
        $returnValue = $this->correctResponses;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002407 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setCorrectResponses
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array responses
     * @return mixed
     */
    public function setCorrectResponses($responses)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 begin
        
    	if(!is_array($responses)){
    		$responses = array($responses);
    	}
    	$this->correctResponses = $responses;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002404 end
    }

    /**
     * Short description of method getMapping
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string type
     * @return array
     */
    public function getMapping($type = '')
    {
        $returnValue = array();

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E3 begin
        
        if($type == 'area'){
        	$returnValue = $this->areaMapping;
        }
        else{
        	$returnValue = $this->mapping;
        }
        
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E3 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setMapping
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array map
     * @param  type
     * @return mixed
     */
    public function setMapping($map, $type = '')
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E5 begin
        
    	if($type == 'area'){
    		$this->areaMapping = $map;
    	}
    	else{
    		$this->mapping = $map;
    	}
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E5 end
    }

    /**
     * Short description of method getMappingDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return double
     */
    public function getMappingDefaultValue()
    {
        $returnValue = (float) 0.0;

        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E8 begin
        
        $returnValue = $this->mappingDefaultValue;
        
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025E8 end

        return (float) $returnValue;
    }

    /**
     * Short description of method setMappingDefaultValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  double value
     * @return mixed
     */
    public function setMappingDefaultValue($value)
    {
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA begin
        
    	$this->mappingDefaultValue = $value;
    	
        // section 127-0-1-1--a2bd9f7:12ae6efc8e9:-8000:00000000000025EA end
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DB begin
        
        $returnValue = parent::toXHTML();
        
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DB end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DD begin
        
        $template = self::getTemplatePath() . 'qti.response.tpl.php';
        $variables 	= $this->extractVariables(); 
        
        $variables['mappingOptions'] = '';
        if(is_array($this->getOption('mapping'))){
        	$variables['mappingOptions'] = $this->xmlizeOptions($this->getOption('mapping'), true);
        }
    	$variables['areaMappingOptions'] = '';
        if(is_array($this->getOption('areaMapping'))){
        	$variables['areaMappingOptions'] = $this->xmlizeOptions($this->getOption('areaMapping'), true);
        }
        $options = $this->getOptions();
        unset($options['mapping']);
        unset($options['areaMapping']);
        $variables['rowOptions'] = $this->xmlizeOptions($options, true);
		
		//parse and render the template
		$tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
		$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1--4fa404a7:12bc4fc4a20:-8000:00000000000026DD end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 127-0-1-1--67198282:12bb0429ae8:-8000:000000000000266C begin
		$qtiService = taoQTI_models_classes_QTI_Service::singleton();
		$interaction = $qtiService->getComposingData($this);
		if(!$interaction instanceof taoQTI_models_classes_QTI_Interaction){
			throw new Exception('cannot find the parent interaction of the current response');
		}
		
		$responseFormClass = 'taoQTI_actions_QTIform_response_'.ucfirst(strtolower($interaction->getType())).'Interaction';
		if(class_exists($responseFormClass)){
			$formContainer = new $responseFormClass($this);
			$myForm = $formContainer->getForm();
			$returnValue = $myForm;
		}
		
		// if(in_array(strtolower($interaction->getType()), array('textentry', 'extendedtext'))){}
		
        // section 127-0-1-1--67198282:12bb0429ae8:-8000:000000000000266C end

        return $returnValue;
    }

    /**
     * get the correct response in JSON format. If no correct response defined
     * null.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function correctToJSON()
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A17 begin
        
        try{
        $correctResponses = $this->getCorrectResponses();
        if (count($correctResponses))
        {
            $returnValue = taoQTI_models_classes_Matching_VariableFactory::createJSONVariableFromQTIData (
        		$this->getIdentifier()
        		, $this->options['cardinality']
        		, $this->options['baseType']
        		, $this->correctResponses
        	);
        }
        }
        catch(Exception $e){}
               
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A17 end

        return $returnValue;
    }

    /**
     * Short description of method areaMapToJson
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function areaMapToJson()
    {
        $returnValue = null;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C82 begin
        
        // Get the stored area mapping
        $mapping = $this->getMapping('area');
        
        if (count ($mapping))
        {
            $returnValue = Array ();
            $returnValue['identifier'] = $this->getIdentifier();
            $returnValue['defaultValue'] = $this->mappingDefaultValue;
            if (isset($this->options['areaMapping']['lowerBound'])){
           		$returnValue['lowerBound'] = $this->options['areaMapping']['lowerBound'];
            }
            if (isset($this->options['areaMapping']['upperBound'])){
            	$returnValue['upperBound'] = $this->options['areaMapping']['upperBound'];
            }
            $mappingValue = Array ();       
            
            // If a mapping has been defined
            if (!empty($mapping))
            {
                foreach ($mapping as $mapKey=>$mappedValue)
                {
                    $areaMapEntryJSON = Array();
                    $areaMapEntryJSON['value'] = (float) $mappedValue["mappedValue"];
                    $areaMapEntryJSON['key'] = taoQTI_models_classes_Matching_VariableFactory::createJSONShapeFromQTIData  ($mappedValue);
                    array_push ($mappingValue, (object) $areaMapEntryJSON);
                }
                $returnValue['value'] = $mappingValue;
            }

            $returnValue = (object) $returnValue;   
        }
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002C82 end

        return $returnValue;
    }

    /**
     * get the mapping in JSON format. If no mapping defined return null.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function mapToJSON()
    {
        $returnValue = null;

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A19 begin
        
    	$mapping = $this->getMapping();
        if (count ($mapping))
        {
            $returnValue = Array ();
            $returnValue['identifier'] = $this->getIdentifier();
            $returnValue['defaultValue'] = $this->mappingDefaultValue;
            if (isset($this->options['mapping']['lowerBound'])){
            	$returnValue['lowerBound'] = intVal($this->options['mapping']['lowerBound']);
            }
            if (isset($this->options['mapping']['upperBound'])){
            	$returnValue['upperBound'] = intVal($this->options['mapping']['upperBound']);
            }
            $mappingValue = Array ();       
            
            // If a mapping has been defined
            if (!empty($mapping))
            {
                foreach ($mapping as $mapKey=>$mappedValue)
                {
                    $mapEntryJSON = Array();
                    $mapEntryJSON['value'] = (float) $mappedValue;
                    $mapEntryJSON['key'] = taoQTI_models_classes_Matching_VariableFactory::createJSONValueFromQTIData  ($mapKey, $this->options['baseType']);
                    array_push ($mappingValue, (object) $mapEntryJSON);
                }
                
                $returnValue['value'] = $mappingValue;
            }
            
            $returnValue = (object) $returnValue;   
        }
    	
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A19 end

        return $returnValue;
    }

    /**
     * get the base type of the response declaration
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getBaseType()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1F begin
        
        $returnValue = $this->options['baseType'];
        
        // section 127-0-1-1-29d6c9d3:12bcdc75857:-8000:0000000000002A1F end

        return (string) $returnValue;
    }

    /**
     * Short description of method getHowMatch
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getHowMatch()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BED begin
        
        $returnValue = $this->howMatch;
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BED end

        return (string) $returnValue;
    }

    /**
     * Short description of method setHowMatch
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string howMatch
     * @return mixed
     */
    public function setHowMatch($howMatch)
    {
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BEF begin
        
        $this->howMatch = $howMatch;
        
        // section 127-0-1-1-703c736:12c63695364:-8000:0000000000002BEF end
    }

} /* end of class taoQTI_models_classes_QTI_Response */

?>