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
 * TAO - taoItems\models\classes\QTI\response\class.ResponseProcessing.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 20.11.2012, 10:19:21 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
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
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('taoQTI/models/classes/QTI/class.Data.php');

/* user defined includes */
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-includes begin
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-includes end

/* user defined constants */
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-constants begin
// section 127-0-1-1--3f4b312:12c5e672d01:-8000:0000000000002BF1-constants end

/**
 * Short description of class
 *
 * @abstract
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_QTI_response
 */
abstract class taoQTI_models_classes_QTI_response_ResponseProcessing
    extends taoQTI_models_classes_QTI_Data
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method create
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Item item
     * @return taoQTI_models_classes_QTI_response_ResponseProcessing
     */
    public static function create( taoQTI_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003624 begin
        throw new common_Exception('create not implemented for '.get_called_class());
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003624 end

        return $returnValue;
    }

    /**
     * Short description of method takeoverFrom
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  ResponseProcessing responseProcessing
     * @param  Item item
     * @return taoQTI_actions_QTIform_ResponseProcessing
     */
    public static function takeoverFrom( taoQTI_models_classes_QTI_response_ResponseProcessing $responseProcessing,  taoQTI_models_classes_QTI_Item $item)
    {
        $returnValue = null;

        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003621 begin
        throw new taoQTI_models_classes_QTI_response_TakeoverFailedException('takeoverFrom not implemented for '.get_called_class());
        // section 127-0-1-1-6f11fd4b:1350ab5145f:-8000:0000000000003621 end

        return $returnValue;
    }

    /**
     * Short description of method getForm
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Response response
     * @return tao_helpers_form_Form
     */
    public function getForm( taoQTI_models_classes_QTI_Response $response)
    {
        $returnValue = null;

        // section 127-0-1-1-7fd95e33:1350eecc263:-8000:0000000000003633 begin
        // section 127-0-1-1-7fd95e33:1350eecc263:-8000:0000000000003633 end

        return $returnValue;
    }

    /**
     * Short description of method takeNoticeOfAddedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfAddedInteraction( taoQTI_models_classes_QTI_Interaction $interaction,  taoQTI_models_classes_QTI_Item $item)
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003659 begin
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003659 end
    }

    /**
     * Short description of method takeNoticeOfRemovedInteraction
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Interaction interaction
     * @param  Item item
     * @return mixed
     */
    public function takeNoticeOfRemovedInteraction( taoQTI_models_classes_QTI_Interaction $interaction,  taoQTI_models_classes_QTI_Item $item)
    {
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003665 begin
        // section 127-0-1-1-53d7bbd:135145c7d03:-8000:0000000000003665 end
    }

} /* end of abstract class taoQTI_models_classes_QTI_response_ResponseProcessing */

?>