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

/**
 * Adapter for CSV format
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @deprecated
 * @package tao
 * @subpackage helpers_data
 */
class tao_helpers_data_GenerisAdapterCsv
    extends tao_helpers_data_GenerisAdapter
{
    /**
     * Short description of attribute loadedFile
     *
     * @access private
     * @var CsvFile
     */
    private $loadedFile = null;
    
    /**
     * Contains the callback functions to be applied on created resources.
     * 
     * @var array
     */
    protected $resourceImported = array();

    /**
     * Instantiates a new tao_helpers_data_GenerisAdapterCSV. The $options array
     * an associative array formated like this:
     * array('field_delimiter' => 'a delimiter char', default is ;,
     * 'field_encloser' => 'a field encloser char, default is "',
     * 'line_break' => 'linebreak escaping sequence, default is \n',
     * 'multi_values_delimiter' => 'a multi values delimiter, default is |',
     * 'first_row_column_names' => 'boolean value describing if the first row
     * column names').
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
    	parent::__construct($options);
    	
    	if(!isset($this->options['field_delimiter'])){
			$this->options['field_delimiter'] = ';';
		}				
		if(!isset($this->options['field_encloser'])){
			$this->options['field_encloser'] = '"';		//double quote
		}
		if(!isset($this->options['line_break'])){
			$this->options['line_break'] = '\n';			// only to display use PHP_EOL in the code for a multi-os compat
		}
		if(!isset($this->options['multi_values_delimiter'])){
			$this->options['multi_values_delimiter'] = '|';
		}
		if(!isset($this->options['first_row_column_names'])){
			$this->options['first_row_column_names'] = true;
		}
		
		// Bind resource callbacks.
		if (isset($this->options['onResourceImported']) && is_array($this->options['onResourceImported'])){
			foreach ($this->options['onResourceImported'] as $callback){
				$this->onResourceImported($callback);
				common_Logger::d("onResourceImported callback added to CSV Adapter");
			}
		}
    }

    /**
     * enable you to load the data in the csvFile to an associative array
     * the options
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string csvFile
     * @return tao_helpers_data_CsvFile
     */
    public function load($csvFile)
    {
        $returnValue = null;

		$csv = new tao_helpers_data_CsvFile($this->options);
		$csv->load($csvFile);
		$this->loadedFile = $csv;
		$returnValue = $this->loadedFile;

        return $returnValue;
    }

    /**
     * Imports the currently loaded CsvFile into the destination Class.
     * The map should be set in the options before executing it.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string source
     * @param  Class destination
     * @return boolean
     */
    public function import($source,  core_kernel_classes_Class $destination = null)
    {
        $returnValue = (bool) false;
        
    	if(!isset($this->options['map'])){
        	throw new BadFunctionCallException("import map not set");
        }
        if(is_null($destination)){
        	throw new InvalidArgumentException("${destination} must be a valid core_kernel_classes_Class");
        }

        $csvData = $this->load($source);
        
        $createdResources = 0;
        $rangeProperty = new core_kernel_classes_Property(RDFS_RANGE);
        
    	for ($rowIterator = 0; $rowIterator < $csvData->count(); $rowIterator++){
    		common_Logger::d("CSV - Importing CSV row ${rowIterator}.");
    		
			$resource = null;
			$csvRow = $csvData->getRow($rowIterator);
			
			//create the instance with the label defined in the map 
			$label = $this->options['map'][RDFS_LABEL];
			
			if($label != 'csv_select' && $label !='csv_null'){
				if(isset($csvRow[$label])){
					$resource = $destination->createInstance($csvRow[$label]);
					common_Logger::t("CSV - Resource creation with label");
				}
			}
			if(is_null($resource)){
				$resource = $destination->createInstance();
				common_Logger::t("CSV - Resource creation without label");
			}
							
			if($resource instanceof core_kernel_classes_Resource){
				common_Logger::t("CSV - Resource successfuly created");
				//import the value of each column into the property defined in the map 
				foreach($this->options['map'] as $propUri => $csvColumn){
					
					if ($propUri != RDFS_LABEL) { // Already set at resource instantiation
					
						$targetProperty = new core_kernel_classes_Property($propUri);
						$ranges = $targetProperty->getPropertyValues($rangeProperty);
						if (count($ranges) > 0) {
							// @todo support multi-valued ranges in CSV import.
							common_Logger::d("CSV - Target property has " . $ranges[0] . " for range");
							$range = new core_kernel_classes_Resource($ranges[0]);
						} 
						else {
						    common_Logger::d("CSV - Target property has no range");
							$range = null;	
						}
						
						if ($range == null || $range->uriResource == RDFS_LITERAL) {
							// Deal with the column value as a literal.
							common_Logger::d("CSV - Importing Literal from CSV");
							$this->importLiteral($targetProperty, $resource, $csvRow, $csvColumn);
						}
						else {
							// Deal with the column value as a resource existing in the Knowledge Base.
							common_Logger::d("CSV - Importing Resource from CSV");
							$this->importResource($targetProperty, $resource, $csvRow, $csvColumn);
						}
					}
				}
				
				// Deal with default values.
				$this->importStaticData($this->options['staticMap'], $this->options['map'], $resource);
				
				// Apply 'resourceImported' callbacks.
				foreach ($this->resourceImported as $callback){
					$callback($resource);
				}
				
				$createdResources++;
			}
		}
        
		$this->addOption('to_import', count($csvData));
		$this->addOption('imported', $createdResources);
		
		if($createdResources > 0){
			$returnValue = true;
		}

        return (bool) $returnValue;
    }

    /**
     * Short description of method export
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Class source
     * @return boolean
     */
    public function export( core_kernel_classes_Class $source = null)
    {
        $returnValue = (bool) false;

        return (bool) $returnValue;
    }

    /**
     * Short description of method importLiteral
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property targetProperty
     * @param  Resource targetResource
     * @param  string csvRow
     * @param  string csvColumn
     * @return mixed
     */
    private function importLiteral( core_kernel_classes_Property $targetProperty,  core_kernel_classes_Resource $targetResource, $csvRow, $csvColumn)
    {
    	if ($csvColumn == 'csv_null' || $csvColumn == 'csv_select') {
    		// We do not use the value contained in $literal but an empty string.
    		common_Logger::d("CSV - Importing an empty string");
    		$targetResource->setPropertyValue($targetProperty, '');
    	}
    	else if (isset($csvRow[$csvColumn]) && $csvRow[$csvColumn] != null) {
    		$literal = $this->applyCallbacks($csvRow[$csvColumn], $this->options, $targetProperty);
            common_Logger::d("CSV - Importing ${literal}");
    		$targetResource->setPropertyValue($targetProperty, $literal);
    	}
    }

    /**
     * Short description of method importResource
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property targetProperty
     * @param  Resource targetResource
     * @param  string csvRow
     * @param  string csvColumn
     * @return mixed
     */
    private function importResource( core_kernel_classes_Property $targetProperty,  core_kernel_classes_Resource $targetResource, $csvRow, $csvColumn)
    {
    	if ($csvColumn != 'csv_select' && $csvColumn != 'csv_null') {
    		
    		// We have to import the cell value as a resource for the target property.
    		$value = $csvRow[$csvColumn];
    		
    		if ($value != null) {
    		    common_Logger::d("CSV - Importing a resource");
    			$value = $this->applyCallbacks($csvRow[$csvColumn], $this->options, $targetProperty);
    			$this->attachResource($targetProperty, $targetResource, $value);
    		}
            else {
                // We have here an exception. The column mapped related to $targetProperty
                // is mapped but not value was found for the current cell. If an entry
                // in the static data map corresponds to the current property, the default
                // value should be used to set the property value.
            }
    	}
        else{
            common_Logger::d("CSV - A default value will be affected.");
        }
    }

    /**
     * Short description of method importStaticData
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array staticMap
     * @param  array map
     * @param  Resource resource
     * @return mixed
     */
    private function importStaticData($staticMap, $map,  core_kernel_classes_Resource $resource)
    {
    	foreach($staticMap as $propUri => $value){
    		if (strpos($propUri, TEMP_SUFFIX_CSV) !== false){
    			$cleanUri = str_replace(TEMP_SUFFIX_CSV, '', $propUri);
    				
    			// If the property was not included in the original CSV file...
    			if(!array_key_exists($cleanUri, $map) || $map[$cleanUri] == 'csv_null' || $map[$cleanUri] == 'csv_select'){
    				if($propUri == RDF_TYPE){
    					$resource->setType(new core_kernel_classes_Class($value));
    				}
    				else{
    					$values = (is_array($value)) ? $value : array($value);
    						
    					foreach ($values as $v){
    						$resource->setPropertyValue(new core_kernel_classes_Property($cleanUri), $v);
    					}
    				}
    			}	
    		}
		}
    }

    /**
     * Short description of method applyCallbacks
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string value
     * @param  array options
     * @param  Property targetProperty
     * @return string
     */
    private function applyCallbacks($value, $options,  core_kernel_classes_Property $targetProperty)
    {
        $returnValue = (string) '';
        
    	if(isset($options['callbacks'])){
			foreach(array('*', $targetProperty->uriResource) as $key){
				if(isset($options['callbacks'][$key]) && is_array($options['callbacks'][$key])){
					foreach ($options['callbacks'][$key] as $callback) {
						if(function_exists($callback)){
							$value = $callback($value);
						}
					}
				}
			}
		}
		
		$returnValue = $value;

        return (string) $returnValue;
    }

    /**
     * Short description of method attachResource
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Property targetProperty
     * @param  Resource targetResource
     * @param  string value
     * @return mixed
     */
    public function attachResource( core_kernel_classes_Property $targetProperty,  core_kernel_classes_Resource $targetResource, $value)
    {
        // We have to check if the resource identified by value exists in the Ontology.
        $resource = new core_kernel_classes_Resource($value);
        if ($resource->exists()) {
        	// Is the range correct ?
        	$valueType = $resource->getType();
        	$targetPropertyRange = $targetProperty->getPropertyValuesCollection(new core_kernel_classes_Property(RDFS_RANGE));
        	$rangeCompliance = true;
        	
        	// If $targetPropertyRange->count = 0, we consider that the resouce
        	// may be attached because $rangeCompliance = true.
        	foreach ($targetPropertyRange->getIterator() as $r) {
        		// Check all classes in target property's range.
        		if (!array_key_exists($r->uriResource, $valueType)) {
        			$rangeCompliance = false;
        			break;
        		}
        	}
        	
        	if (true == $rangeCompliance) {
        		$targetResource->setPropertyValue($targetProperty, $resource->uriResource);
        	}
        }
    }

    /**
     * Sets the currently loaded CsvFile.
     *
     * @access protected
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  CsvFile csvFile
     * @return void
     */
    protected function setCsvFile( tao_helpers_data_CsvFile $csvFile)
    {

    }

    /**
     * Gets the currently loaded CsvFile.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return tao_helpers_data_CsvFile
     */
    public function getCsvFile()
    {
		return null;
    }
    
    public function onResourceImported(Closure $closure) {
		$this->resourceImported[] = $closure;
	}
}

?>