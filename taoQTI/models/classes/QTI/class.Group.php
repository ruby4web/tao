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
 * A group is an concept to enable choice logical grouping (ordering). 
 * It use when there is distinct choices groups in an interaction.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * A choice is a kind of interaction's proposition.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10254
 */
require_once('taoQTI/models/classes/QTI/class.Choice.php');

/**
 * The QTI_Data class represent the abstract model for all the QTI objects.
 * It contains all the attributes of the different kind of QTI objects.
 * It manages the identifiers and serial creation.
 * It provides the serialisation and persistance methods.
 * And give the interface for the rendering.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/class.Data.php');

/**
 * The QTI's interactions are the way the user interact with the system. The
 * will be rendered into widgets to enable the user to answer to the item.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10247
 */
require_once('taoQTI/models/classes/QTI/class.Interaction.php');

/* user defined includes */
// section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002546-includes begin
// section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002546-includes end

/* user defined constants */
// section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002546-constants begin
// section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002546-constants end

/**
 * A group is an concept to enable choice logical grouping (ordering). 
 * It use when there is distinct choices groups in an interaction.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI
 */
class taoQTI_models_classes_QTI_Group
    extends taoQTI_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---
    // generateAssociationEnd :     // generateAssociationEnd : 

    // --- ATTRIBUTES ---

    /**
     * Short description of attribute choices
     *
     * @access protected
     * @var array
     */
    protected $choices = array();

    // --- OPERATIONS ---

    /**
     * Short description of method getChoices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return array
     */
    public function getChoices()
    {
        $returnValue = array();

        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002578 begin
        
        $returnValue = $this->choices;
        
        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:0000000000002578 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setChoices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array choices
     * @return mixed
     */
    public function setChoices($choices)
    {
        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:000000000000257A begin
        
    	$this->choices = array();
    	$this->addChoices($choices);
    	
        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:000000000000257A end
    }

    /**
     * Short description of method addChoices
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  array choices
     * @return mixed
     */
    public function addChoices($choices)
    {
        // section 127-0-1-1-4b2a2e4c:12b61a11fd4:-8000:00000000000025AC begin
        
    	$groupElements = array();
    	foreach($choices as $choice){
    		if($choice instanceof taoQTI_models_classes_QTI_Choice){
				$this->choices[] = $choice->getSerial();
    		}else if(is_string($choice)){
				$this->choices[] = $choice;
			}else{
				throw new InvalidArgumentException("the choices parameter should be a list of taoQTI_models_classes_QTI_Choice or of string");
			}
    	}
    	
        // section 127-0-1-1-4b2a2e4c:12b61a11fd4:-8000:00000000000025AC end
    }

    /**
     * Short description of method removeChoice
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Choice choice
     * @return boolean
     */
    public function removeChoice( taoQTI_models_classes_QTI_Choice $choice)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-15fd4bad:12b579443b6:-8000:00000000000025A9 begin
		
		if(!is_null($choice)){
			$key = array_search($choice->getSerial(), $this->choices);
    		if($key !== false){
			
				unset($this->choices[$key]);
				
				//remove the choice from the group data:
				$this->setData(str_replace("{{$choice->getSerial()}}", '', $this->getData()));
				
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-15fd4bad:12b579443b6:-8000:00000000000025A9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method toXHTML
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toXHTML()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-2f988aef:12b80ad1e72:-8000:00000000000025BE begin
        
     	$template = self::getTemplatePath() . 'groups/xhtml.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 $template = self::getTemplatePath() . 'xhtml.group.tpl.php';
        }
    	$variables = $this->extractVariables();
    	if($this->type != 'gap'){
    		$variables['data'] = '';
        	foreach($this->getChoices() as $choiceSerial){
				$variables['data'] .= "{{$choiceSerial}}";
			}
        }
        	
		
		$tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
      	$returnValue = $tplRenderer->render();
        
        // section 127-0-1-1-2f988aef:12b80ad1e72:-8000:00000000000025BE end

        return (string) $returnValue;
    }

    /**
     * Short description of method toQTI
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return string
     */
    public function toQTI()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:000000000000257F begin
        
        //check first if there is a template for the given type
        $template = self::getTemplatePath() . 'groups/qti.' .strtolower($this->type) . '.tpl.php';
        if(!file_exists($template)){
        	 $template = self::getTemplatePath() . 'qti.group.tpl.php';
        }
        
        //get the variables to used in the template
        $variables = array(
        	'identifier'	=> $this->identifier,
        	'type'			=> $this->type,
        	'data'			=> $this->data,
        	'options'		=> $this->options,
        	'rowOptions'	=> $this->xmlizeOptions()
        );
        
        if($this->type == 'simpleMatchSet'){
        	$variables['identifier'] = false;
        }
        
        if($this->type != 'gap' && $this->type != 'associableHotspots'){//associableHotspots in graphic gap match interactions are groups
        	$variables['data'] = '';
        	foreach($this->getChoices() as $choiceSerial){
                        $variables['data'] .= "{{$choiceSerial}}";
                }
        }
	
        //parse and render the template
        $tplRenderer = new taoItems_models_classes_TemplateRenderer($template, $variables);
        $returnValue = $tplRenderer->render();
        
        // section 127-0-1-1-7bfc492a:12ad2946c72:-8000:000000000000257F end

        return (string) $returnValue;
    }

    /**
     * Short description of method toForm
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return tao_helpers_form_xhtml_Form
     */
    public function toForm()
    {
        $returnValue = null;

        // section 10-13-1-39-51571c01:12b7726bac6:-8000:00000000000028D4 begin
		$choiceType = $this->getType();
		$choiceFormClass = 'taoQTI_actions_QTIform_choice_'.ucfirst($choiceType);//gap for gap match interaction or associableHotspot for graphic gap match interaction
		if(!class_exists($choiceFormClass)){
			throw new Exception("the class {$choiceFormClass} does not exist");
		}else{
			$formContainer = new $choiceFormClass($this);
			$myForm = $formContainer->getForm();
			$returnValue = $myForm;
		}
        // section 10-13-1-39-51571c01:12b7726bac6:-8000:00000000000028D4 end

        return $returnValue;
    }

} /* end of class taoQTI_models_classes_QTI_Group */

?>