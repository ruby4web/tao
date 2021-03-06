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
 * TAO -
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 24.01.2012, 17:13:08 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/response/interactionResponseProcessing/class.Template.php');

/* user defined includes */
// section 127-0-1-1-786830e4:134f066fb13:-8000:000000000000900A-includes begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:000000000000900A-includes end

/* user defined constants */
// section 127-0-1-1-786830e4:134f066fb13:-8000:000000000000900A-constants begin
// section 127-0-1-1-786830e4:134f066fb13:-8000:000000000000900A-constants end

/**
 * Short description of class
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response_interactionResponseProcessing
 */
class taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate
    extends taoQTI_models_classes_QTI_response_interactionResponseProcessing_Template
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute CLASS_ID
     *
     * @access public
     * @var string
     */
    const CLASS_ID = 'mappoint';

    // --- OPERATIONS ---

    /**
     * Short description of method getRule
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getRule()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009015 begin
        $returnValue = 'if(isNull(null, getResponse("'.$this->getResponse()->getIdentifier().'"))) { '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", 0); } else { '.
        	'setOutcomeValue("'.$this->getOutcome()->getIdentifier().'", '.
        		'mapResponsePoint(null, getMap("'.$this->getResponse()->getIdentifier().'", "area"), getResponse("'.$this->getResponse()->getIdentifier().'"))); };';
        // section 127-0-1-1-786830e4:134f066fb13:-8000:0000000000009015 end

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

        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000362C begin
        $returnValue = '<responseCondition>
		    <responseIf>
		        <not>
		            <isNull>
		                <variable identifier="'.$this->getResponse()->getIdentifier().'" />
		            </isNull>
		        </not>
		        <setOutcomeValue identifier="'.$this->getOutcome()->getIdentifier().'">
	                <mapResponsePoint identifier="'.$this->getResponse()->getIdentifier().'" />
		        </setOutcomeValue>
		    </responseIf>
		</responseCondition>';
        // section 127-0-1-1-4c0a0972:134fa47975d:-8000:000000000000362C end

        return (string) $returnValue;
    }

} /* end of class taoQTI_models_classes_QTI_response_interactionResponseProcessing_MapResponsePointTemplate */

?>