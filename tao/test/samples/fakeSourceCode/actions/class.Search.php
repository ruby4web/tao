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
 * Create a form to search the resources of the ontology
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Create a form from a  resource of your ontology. 
 * Each property will be a field, regarding it's widget.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/actions/form/class.Instance.php');

/* user defined includes */
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:0000000000002494-includes begin
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:0000000000002494-includes end

/* user defined constants */
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:0000000000002494-constants begin
// section 127-0-1-1-56df1631:1284f2fd9c5:-8000:0000000000002494-constants end

/**
 * Create a form to search the resources of the ontology
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package tao
 * @subpackage actions_form
 */
class tao_actions_form_Search
    extends tao_actions_form_Instance
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Initialize the form
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initForm()
    {
        // section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000249F begin
        
    	(isset($this->options['name'])) ? $name = $this->options['name'] : $name = ''; 
    	if(empty($name)){
			$name = 'form_'.(count(self::$forms)+1);
		}
		unset($this->options['name']);
			
		$this->form = tao_helpers_form_FormFactory::getForm($name, $this->options);
		
		//search action in toolbar
		$searchElt = tao_helpers_form_FormFactory::getElement('search', 'Free');
		$searchElt->setValue("<a href='#' class='form-submiter' ><img src='".TAOBASE_WWW."/img/search.png'  /> ".__('Search')."</a>");
		$this->form->setActions(array($searchElt), 'top');
		$this->form->setActions(array($searchElt), 'bottom');
    	
        // section 127-0-1-1-56df1631:1284f2fd9c5:-8000:000000000000249F end
    }

    /**
     * Initialize the form elements
     *
     * @access protected
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    protected function initElements()
    {
        // section 127-0-1-1-56df1631:1284f2fd9c5:-8000:00000000000024A1 begin
        
    	$chainingElt = tao_helpers_form_FormFactory::getElement('chaining', 'Radiobox');
		$chainingElt->setDescription(__('Filtering mode'));
		$chainingElt->setOptions(array('or' =>  __('Exclusive (OR)'), 'and' => __('Inclusive (AND)')));
		$chainingElt->setValue('or');
		$this->form->addElement($chainingElt);
		
		$recursiveElt = tao_helpers_form_FormFactory::getElement('recursive', 'Radiobox');
		$recursiveElt->setDescription(__('Recursive'));
		$recursiveElt->setOptions(array('0' => __('Current class only'), '10' =>  __('Current class + Subclasses')));
		$recursiveElt->setValue('current');
		$this->form->addElement($recursiveElt);
		
		$langElt = tao_helpers_form_FormFactory::getElement('lang', 'Combobox');
		$langElt->setDescription(__('Language'));
		
		$languages = array_merge(array('  '), tao_helpers_I18n::getAvailableLangs(true));
		$langElt->setOptions($languages);
		$langElt->setValue(0);
		$this->form->addElement($langElt);
		
		$this->form->createGroup('params', __('Options'), array('chaining', 'recursive', 'lang'));
		
		
		$filters = array();
		
		$descElt = tao_helpers_form_FormFactory::getElement('desc', 'Label');
		$descElt->setValue(__('Use the * character to replace any string'));
		$this->form->addElement($descElt);
		$filters[] = 'desc';
		
		$defaultProperties 	= tao_helpers_form_GenerisFormFactory::getDefaultProperties();
		$classProperties	= tao_helpers_form_GenerisFormFactory::getClassProperties($this->clazz, $this->getTopClazz());
		
		$properties = array_merge($defaultProperties, $classProperties);
		
		(isset($this->options['recursive'])) ? $recursive = $this->options['recursive'] : $recursive = false;
		if($recursive){
			foreach($this->clazz->getSubClasses(true) as $subClass){
				$properties = array_merge($subClass->getProperties(false), $properties);
			}
		}
		
		foreach($properties as $property){
	
			$element = tao_helpers_form_GenerisFormFactory::elementMap($property);
			if( ! is_null($element) && 
				! $element instanceof tao_helpers_form_elements_Authoring && 
				! $element instanceof tao_helpers_form_elements_Hiddenbox &&
				! $element instanceof tao_helpers_form_elements_Hidden ){
				
				if($element instanceof tao_helpers_form_elements_MultipleElement){
					$newElement = tao_helpers_form_FormFactory::getElement($element->getName(), 'Checkbox');
					$newElement->setDescription($element->getDescription());
					$newElement->setOptions($element->getOptions());
					$element = $newElement;
				}
				if($element instanceof tao_helpers_form_elements_Htmlarea){
					$newElement = tao_helpers_form_FormFactory::getElement($element->getName(), 'Textarea');
					$newElement->setDescription($element->getDescription());
					$element = $newElement;
				}
				
				$this->form->addElement($element);
				$filters[] = $element->getName();
			}
		}
		$this->form->createGroup('filters', __('Filters'), $filters);
    	
        // section 127-0-1-1-56df1631:1284f2fd9c5:-8000:00000000000024A1 end
    }

} /* end of class tao_actions_form_Search */

?>