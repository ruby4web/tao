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
 * TAO - taoQTI/actions/QTIform/class.AddObject.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 21.06.2012, 17:39:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * This class provide a container for a specific form instance.
 * It's subclasses instanciate a form and it's elements to be used as a
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('tao/helpers/form/class.FormContainer.php');

/* user defined includes */
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-includes begin
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-includes end

/* user defined constants */
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-constants begin
// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2E-constants end

/**
 * Short description of class taoQTI_actions_QTIform_AddObject
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage actions_QTIform
 */
class taoQTI_actions_QTIform_AddObject
    extends tao_helpers_form_FormContainer
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initForm
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initForm()
    {
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2F begin
        $this->form = tao_helpers_form_FormFactory::getForm('AddObjectForm');
		$this->form->setActions(array(), 'bottom');
		// section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B2F end
    }

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    public function initElements()
    {
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B31 begin
        $itemElt = tao_helpers_form_FormFactory::getElement('itemSerial', 'Hidden');
		$itemElt->setValue($this->data['itemSerial']);
		$this->form->addElement($itemElt);
		
    	//title:
		$urlElt = tao_helpers_form_FormFactory::getElement('objecturl', 'Textbox');
		$urlElt->setDescription(__('URL'));
		$urlElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('NotEmpty'),
			tao_helpers_form_FormFactory::getValidator('Url')	
		));
		$this->form->addElement($urlElt);
		
		$heightElt = tao_helpers_form_FormFactory::getElement('height', 'Textbox');
		$heightElt->setDescription(__('Heihgt'));
		$heightElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('Integer')
		));
		$this->form->addElement($heightElt);
		
		$widthElt = tao_helpers_form_FormFactory::getElement('width', 'Textbox');
		$widthElt->setDescription(__('Width'));
		$widthElt->addValidators(array(
			tao_helpers_form_FormFactory::getValidator('Integer')
		));
		$this->form->addElement($widthElt);
		/*
		$typeElt = tao_helpers_form_FormFactory::getElement('type', 'Textbox');
		$typeElt->setDescription(__('Type'));
		$this->form->addElement($typeElt);
		*/
        // section 127-0-1-1--451f3cfb:1380fae694a:-8000:0000000000003B31 end
    }

} /* end of class taoQTI_actions_QTIform_AddObject */

?>