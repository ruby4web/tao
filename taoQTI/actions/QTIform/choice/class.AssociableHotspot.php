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
 * TAO - taoItems\actions\QTIform\choice\class.AssociableHotspot.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 05.01.2011, 11:32:47 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10317
 * @subpackage actions_QTIform_choice
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include taoQTI_actions_QTIform_choice_AssociableChoice
 *
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10257
 */
require_once('taoQTI/actions/QTIform/choice/class.AssociableChoice.php');

/* user defined includes */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005052-includes begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005052-includes end

/* user defined constants */
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005052-constants begin
// section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005052-constants end

/**
 * Short description of class taoQTI_actions_QTIform_choice_AssociableHotspot
 *
 * @access public
 * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
 * @package taoItems
 * @see http://www.imsglobal.org/question/qti_v2p0/imsqti_infov2p0.html#element10317
 * @subpackage actions_QTIform_choice
 */
class taoQTI_actions_QTIform_choice_AssociableHotspot
    extends taoQTI_actions_QTIform_choice_AssociableChoice
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method initElements
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     */
    public function initElements()
    {
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005053 begin
		
		parent::setCommonElements();
		
		//add hotspot label:
		$labelElt = tao_helpers_form_FormFactory::getElement('hotspotLabel', 'Textbox');
		$labelElt->setDescription(__('Label'));
		$labelElt->setValue($this->choice->getOption('hotspotLabel'));
		$this->form->addElement($labelElt);
		
		$shapeElt = tao_helpers_form_FormFactory::getElement('shape', 'Combobox');
		$shapeElt->setDescription(__('Shape'));
		$shapeElt->setAttribute('class', 'qti-shape');
		$shapeElt->setOptions(array(
			'default' => __('default'),
			'circle' => __('circle'),
			'ellipse' => __('ellipse'),
			'rect' => __('rectangle'),
			'poly' => __('polygon')
		));
		$shapeElt->setValue($this->choice->getOption('shape'));
		$this->form->addElement($shapeElt);
		
		$coordsElt = tao_helpers_form_FormFactory::getElement('coords', 'Hidden');
		$coordsElt->setValue($this->choice->getOption('coords'));
		$this->form->addElement($coordsElt);
		
		$matchMaxElt = tao_helpers_form_FormFactory::getElement('matchMax', 'Textbox');
		$matchMaxElt->setDescription(__('Maximal number of matching'));
		$matchMax = (string) $this->choice->getOption('matchMax');
		$matchMaxElt->setValue($matchMax);
		$this->form->addElement($matchMaxElt);
		
		$this->form->createGroup('choicePropOptions_'.$this->choice->getSerial(), __('Advanced properties'), array('hotspotLabel', 'fixed', 'matchGroup', 'matchMax'));
	
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005053 end
    }

    /**
     * Short description of method getMatchGroupOptions
     *
     * @access public
     * @author Somsack SIPASSEUTH, <s.sipasseuth@gmail.com>
     * @return array
     */
    public function getMatchGroupOptions()
    {
        $returnValue = array();

        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005055 begin
		
		foreach($this->interaction->getChoices() as $choice){
			$returnValue[$choice->getIdentifier()] = $choice->getIdentifier();
		}
		
        // section 10-13-1-39-643eb156:12d51696e7c:-8000:0000000000005055 end

        return (array) $returnValue;
    }

} /* end of class taoQTI_actions_QTIform_choice_AssociableHotspot */

?>