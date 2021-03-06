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
 * Ellipse represents ellipse managed by the tao matching api.
 * Ellipse is used to represent ellipse and circle.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Shape represents the different shapres managed by the tao 
 * matching api.
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('taoQTI/models/classes/Matching/class.Shape.php');

/* user defined includes */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CAB-includes begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CAB-includes end

/* user defined constants */
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CAB-constants begin
// section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CAB-constants end

/**
 * Ellipse represents ellipse managed by the tao matching api.
 * Ellipse is used to represent ellipse and circle.
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package taoItems
 * @subpackage models_classes_Matching
 */
class taoQTI_models_classes_Matching_Ellipse
    extends taoQTI_models_classes_Matching_Shape
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Center of the Ellipse
     *
     * @access protected
     * @var Tuple
     */
    protected $center = null;

    /**
     * Horizontal radius of the ellipse
     *
     * @access protected
     * @var Integer
     */
    protected $hradius = null;

    /**
     * Vertical radius of the ellipse
     *
     * @access protected
     * @var Integer
     */
    protected $vradius = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  data
     * @return mixed
     */
    public function __construct(   $data)
    {
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CC7 begin

        $this->setType ($data->type);
        $this->hradius = $data->hradius;
        $this->vradius = $data->vradius;
        $this->center = taoQTI_models_classes_Matching_VariableFactory::create($data->center);
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CC7 end
    }

    /**
     * Check if the polygon contains a point
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Variable var
     * @return boolean
     * @see The point to find.
The point is represented by a "Tuple Variable" of two integers.
     */
    public function contains( taoQTI_models_classes_Matching_Variable $var)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CB7 begin
        
        list ($point_x, $point_y) = $var->getValue();
        list ($center_x, $center_y) = $this->getCenter()->getValue();
        
        $a = pow($point_x->getValue()-$center_x->getValue(),2)/pow($this->getHRadius(),2);
        $b = pow($point_y->getValue()-$center_y->getValue(),2)/pow($this->getVRadius(),2);
        $returnValue = $a+$b <= 1;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CB7 end

        return (bool) $returnValue;
    }

    /**
     * Get the horizontal radius
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_form_validators_Integer
     */
    public function getHRadius()
    {
        $returnValue = null;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CCD begin
        
        return $this->hradius;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CCD end

        return $returnValue;
    }

    /**
     * Get the vertical radius
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return tao_helpers_form_validators_Integer
     */
    public function getVRadius()
    {
        $returnValue = null;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD0 begin
        
        return $this->vradius;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD0 end

        return $returnValue;
    }

    /**
     * Get the center point
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return taoQTI_models_classes_Matching_Tuple
     */
    public function getCenter()
    {
        $returnValue = null;

        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD2 begin
        
        return $this->center;
        
        // section 127-0-1-1--1f4c3271:12ce9f13e78:-8000:0000000000002CD2 end

        return $returnValue;
    }

} /* end of class taoQTI_models_classes_Matching_Ellipse */

?>