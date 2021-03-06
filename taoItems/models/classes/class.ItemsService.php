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
 * Service methods to manage the Items business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package taoItems
 * @subpackage models_classes
 */
class taoItems_models_classes_ItemsService
    extends tao_models_classes_GenerisService
{

    /**
     * The RDFS top level item class
     *
     * @access protected
     * @var Class
     */
    protected $itemClass = null;

    /**
     * the instance of the itemModel property
     *
     * @access protected
     * @var Property
     */
    protected $itemModelProperty = null;

    /**
     * the instance of the itemContent property
     *
     * @access public
     * @var Property
     */
    public $itemContentProperty = null;

    /**
     * key to use to store dthe default filesource
     * to be used in for new items
     *
     * @access private
     * @var string
     */
    const CONFIG_DEFAULT_FILESOURCE = 'defaultItemFileSource';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return void
     */
    public function __construct()
    {
		parent::__construct();
		$this->itemClass			= new core_kernel_classes_Class( TAO_ITEM_CLASS );
		$this->itemModelProperty	= new core_kernel_classes_Property(TAO_ITEM_MODEL_PROPERTY);
		$this->itemContentProperty	= new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY);
    }

    public function getRootClass() {
    	return $this->itemClass;
    }
    
    /**
     * get an item subclass by uri. 
     * If the uri is not set, it returns the  item class (the top level class.
     * If the uri don't reference an item subclass, it returns null
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string uri
     * @return core_kernel_classes_Class
     * @deprecated
     */
    public function getItemClass($uri = '')
    {
        $returnValue = null;

		if(empty($uri) && !is_null($this->itemClass)){
			$returnValue= $this->itemClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isItemClass($clazz)){
				$returnValue = $clazz;
			}
		}

        return $returnValue;
    }

    /**
     * check if the class is a or a subclass of an Item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function isItemClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if($this->itemClass->uriResource == $clazz->uriResource){
			return true;
		}

		foreach($clazz->getParentClasses(true) as $parent){

			if($parent->uriResource == $this->itemClass->uriResource){
				$returnValue = true;
				break;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * delete an item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function deleteItem( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

		if(!is_null($item)){

			$returnValue = $this->deleteItemContent($item);

			//TODO : should the runtimeFolder be language dependent as well?
			$runtimeFolder = $this->getRuntimeFolder($item);
			if (is_dir($runtimeFolder)) {
				tao_helpers_File::remove($runtimeFolder, true);
			}

			$returnValue &= $item->delete(true);

		}

        return (bool) $returnValue;
    }

    /**
     * delete an item class or subclass
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteItemClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

		if(!is_null($clazz)){
			if($this->isItemClass($clazz)){
				$returnValue = $clazz->delete();
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Short description of method getDefaultItemFolder
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string lang
     * @return string
     */
    public function getDefaultItemFolder( core_kernel_classes_Resource $item, $lang = '')
    {
        $returnValue = (string) '';

		if(!is_null($item)){

			if(empty($lang)){
				$session = core_kernel_classes_Session::singleton();
				$lang = $session->getDataLanguage();
			}

			$itemRepo = $this->getDefaultFileSource();
			$repositoryPath = $itemRepo->getPath();
			$repositoryPath = substr($repositoryPath,strlen($repositoryPath)-1,1)==DIRECTORY_SEPARATOR ? $repositoryPath : $repositoryPath.DIRECTORY_SEPARATOR;
			$returnValue = $repositoryPath.tao_helpers_Uri::getUniqueId($item->uriResource).DIRECTORY_SEPARATOR.'itemContent'.DIRECTORY_SEPARATOR.$lang;

		}

        return (string) $returnValue;
    }

    /**
     * Short description of method getRuntimeFolder
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return string
     */
    public function getRuntimeFolder( core_kernel_classes_Resource $item)
    {
        $returnValue = (string) '';

		if(!is_null($item)){
			$folderName = substr($item->uriResource, strpos($item->uriResource, '#') + 1);
			$basePreview = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems')->getConstant('BASE_PREVIEW');
			$returnValue = $basePreview . $folderName;
		}

        return (string) $returnValue;
    }

    /**
     * define the content of item to be inserted by default (to prevent null
     * after creation)
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function setDefaultItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = null;
		
		//@todo : to be completed
		$returnValue = $item;

        return $returnValue;
    }

    /**
     * Enables you to get the content of an item, 
     * usually an xml string
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  boolean preview
     * @param  string lang
     * @return string
     */
    public function getItemContent( core_kernel_classes_Resource $item, $preview = false, $lang = '')
    {
        $returnValue = (string) '';

		common_Logger::i('Get itemContent for item '.$item->getUri());

		if(!is_null($item)){

			$itemContent = null;

			if(empty($lang)){
				$itemContents = $item->getPropertyValuesCollection($this->itemContentProperty);
			}
			else{
				$itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
			}

			if($itemContents->count() > 0){
				$itemContent = $itemContents->get(0);
			}

			if(!is_null($itemContent) && $this->isItemModelDefined($item)){

				if(core_kernel_classes_File::isFile($itemContent)){

					$file = new core_kernel_classes_File($itemContent->getUri());
					$returnValue = file_get_contents($file->getAbsolutePath());
					if ($returnValue == false) {
						common_Logger::w('File '.$file->getAbsolutePath().' not found for fileressource '.$itemContent->getUri());
					}
				}
			} else {
				common_Logger::w('No itemContent for item '.$item->getUri());
			}
		}

        return (string) $returnValue;
    }

    /**
     * Check if the item has an itemContent Property
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string lang
     * @return boolean
     */
    public function hasItemContent( core_kernel_classes_Resource $item, $lang = '')
    {
        $returnValue = (bool) false;

		if(!is_null($item)){

			if(empty($lang)){
				$lang = $this->getSessionLg();
			}

			$itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
			$returnValue = ($itemContents->count() > 0);
		}

        return (bool) $returnValue;
    }

    /**
     * Short description of method setItemContent
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string content
     * @param  string lang
     * @param  string commitMessage
     * @return boolean
     */
    public function setItemContent( core_kernel_classes_Resource $item, $content, $lang = '', $commitMessage = '')
    {
        $returnValue = (bool) false;

		if(is_null($item) && !$this->isItemModelDefined($item)) {
			throw new common_exception_Error('No item or itemmodel in '.__FUNCTION__);
		}
		
		$lang = empty($lang) ? $lang = $this->getSessionLg() : $lang;
		$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
		$dataFile = (string)$itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));

		if($this->hasItemContent($item, $lang)){

			$itemContents = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
			$itemContent = $itemContents->get(0);
			if(!core_kernel_classes_File::isFile($itemContent)){
				throw new common_Exception('Item '.$item->getUri().' has none file itemContent');
			}
			$file = new core_kernel_versioning_File($itemContent);
			$returnValue = $file->setContent($content);
			
		} else {

			$versionedFileClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE);
			$repository = $this->getDefaultFileSource();
			$file = $repository->createFile(
				$dataFile,
				tao_helpers_Uri::getUniqueId($item->getUri()) . DIRECTORY_SEPARATOR . 'itemContent' . DIRECTORY_SEPARATOR . $lang
			);
			$item->setPropertyValueByLg($this->itemContentProperty, $file->getUri(), $lang);
			$file->setContent($content);
			$returnValue = $file->add(true, true);
		}
		
		if ($commitMessage != 'HOLD_COMMIT'){//hack to control commit or not
			$returnValue = $file->commit($commitMessage);
		}	

        return (bool) $returnValue;
    }

    /**
     * Check if the Item has on of the itemModel property in the models array
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array models the list of URI of the itemModel to check
     * @return boolean
     */
    public function hasItemModel( core_kernel_classes_Resource $item, $models)
    {
        $returnValue = (bool) false;

		$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
		if($itemModel instanceof core_kernel_classes_Resource){
			if(in_array($itemModel->uriResource, $models)){
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Check if the itemModel has been defined for that item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function isItemModelDefined( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

		if(!is_null($item)){

			$model = $item->getOnePropertyValue($this->itemModelProperty);
		 	if ($model instanceof core_kernel_classes_Literal){
				if(strlen((string)$model) > 0){
					$returnValue = true;
				}
			}
			else if(!is_null($model)){
				$returnValue = true;
			}

		}

        return (bool) $returnValue;
    }

    /**
     * Get the runtime associated to the item model.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getModelRuntime( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

		if(!is_null($item)){
			$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
			if(!is_null($itemModel)){
				$returnValue = $itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_RUNTIME_PROPERTY));
			}
		}

        return $returnValue;
    }

    /**
     * Short description of method hasModelStatus
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array status
     * @return boolean
     */
    public function hasModelStatus( core_kernel_classes_Resource $item, $status)
    {
        $returnValue = (bool) false;

		if(!is_null($item)){
			if(!is_array($status) && is_string($status)){
				$status = array($status);
			}
			try{
				$itemModel = $item->getUniquePropertyValue($this->itemModelProperty);
				if($itemModel instanceof core_kernel_classes_Resource){
					$itemModelStatus = $itemModel->getUniquePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_STATUS_PROPERTY));
					if(in_array($itemModelStatus->uriResource, $status)){
						$returnValue = true;
					}
				}
			}
			catch(common_Exception $ce){
				$returnValue = false;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Deploy the item in parameter into the path.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string path
     * @param  string url
     * @param  array parameters
     * @return boolean
     */
    public function deployItem( core_kernel_classes_Resource $item, $path, $url = '', $parameters = array())
    {
        $returnValue = (bool) false;

		if(!is_null($item)){

			//parameters that could not be rewrited
			if(!isset($parameters['root_url']))		{ $parameters['root_url'] 		= ROOT_URL; }
			if(!isset($parameters['base_www']))		{ $parameters['base_www'] 		= BASE_WWW; }
			if(!isset($parameters['taobase_www']))	{ $parameters['taobase_www'] 	= TAOBASE_WWW; }
			if(!isset($parameters['debug']))		{ $parameters['debug'] 			= false; }
			if(!isset($parameters['raw_preview']))	{ $parameters['raw_preview'] 	= false; }

			taoItems_models_classes_TemplateRenderer::setContext($parameters, 'ctx_');

			$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
			$service = $this->getItemModelImplementation($itemModel);
			if (!is_null($service)) {
				$output = $service->render($item);
				$itemFolder = dirname($path);
				
				//replace relative paths to resources by absolute uris to help the compilator
				$matches = array();
				$output = preg_replace_callback("/(href|src|data|\['imagePath'\]|root_url)\s*=\s*[\"\'](?!http|ftp|#)([\.\/]*)(.+?)[\"\']/i", function($matches) use ($url){
					
					$returnValue = $matches[0];
					$htmlAttr = $matches[1];
					$relUri = $matches[3];
					
					if(trim($relUri) != '' && true){
						if (preg_match('/(.)+\/filemanager\/views\/data\//i', $relUri)) {
							//check if the file is contained in the file manager
							$absoluteUri = preg_replace('/(.)+\/filemanager\/views\/data\//i', ROOT_URL . '/filemanager/views/data/', $relUri);
						} else {
							$absoluteUri = dirname($url) . '/' . $relUri;
						}
						
						$returnValue = $htmlAttr.'="'.$absoluteUri.'"';
					}
						
					return $returnValue;
					
				}, $output);

				if(file_put_contents($path, $output)){
					$returnValue = true;
				}

				if($returnValue){

					$itemFileName = '';
					$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
					if(!is_null($itemModel)){
						$itemFileName = (string)$itemModel->getOnePropertyValue(new core_kernel_classes_Property(TAO_ITEM_MODEL_DATAFILE_PROPERTY));
					}

					//copy the resources
					$sourceFolder = $this->getItemFolder($item);
					foreach(scandir($sourceFolder) as $file){
						if($file != basename($path) && $file != $itemFileName &&$file != '.' && $file != '..'){
							$copyFromPath = $sourceFolder . '/'. $file;
							$copyToPath = $itemFolder . '/' . $file;
							tao_helpers_File::copy($copyFromPath, $copyToPath, true);
						}
					}

					//copy the event.xml if not present
					if(!file_exists($itemFolder.'/events.xml')){
						$eventXml = file_get_contents(ROOT_PATH.'/taoItems/data/events_ref.xml');
						if(is_string($eventXml) && !empty($eventXml)){
							$eventXml = str_replace('{ITEM_URI}', $item->uriResource, $eventXml);
							$copyEventsToPath = $itemFolder.'/events.xml';
							@file_put_contents($copyEventsToPath, $eventXml);
						}
					}
				}
			} else {
				common_Logger::w('Deploy not possible for item('.$item->getUri().')');
			}
			
		}

        return (bool) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFileUriByItem($itemUri)
    {
        $returnValue = (string) '';

		if(strlen($itemUri) > 0){
			$returnValue = BASE_DATA.'/'.tao_helpers_Uri::encode($itemUri).'.xml';
		}

        return (string) $returnValue;
    }

    /**
     * get the item uri linked to the given file
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string uri
     * @return string
     */
    public function getAuthoringFileItemByUri($uri)
    {
        $returnValue = (string) '';

		if(strlen($uri) > 0){
			if(file_exists($uri)){
				$returnValue = tao_helpers_Uri::decode(
					str_replace(BASE_DATA, '',
						str_replace('.xml', '', $uri)
					)
				);
			}
		}

        return (string) $returnValue;
    }

    /**
     * Get the file linked to an item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string itemUri
     * @return string
     */
    public function getAuthoringFile($itemUri)
    {
        $returnValue = (string) '';

		$uri = $this->getAuthoringFileUriByItem($itemUri);

		if(!file_exists($uri)){
			file_put_contents($uri, '<?xml version="1.0" encoding="utf-8" ?>');
		}
		$returnValue = $uri;

        return (string) $returnValue;
    }

    /**
     * Service to get the temporary authoring file
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string itemUri
     * @param  boolean fallback
     * @return string
     */
    public function getTempAuthoringFile($itemUri, $fallback = false)
    {
        $returnValue = (string) '';

		if(strlen($itemUri) > 0){
			$returnValue = BASE_DATA.'tmp_'.tao_helpers_Uri::encode($itemUri).'.xml';
			if(!file_exists($returnValue)){
				if($fallback){	//fallback in case of error otheerwise create  the file
					$returnValue = $this->getAuthoringFile($itemUri);
				}
			}
		}

        return (string) $returnValue;
    }

    /**
     * Service to get the matching data of an item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource itemRdf
     * @return array
     */
    public function getMatchingData( core_kernel_classes_Resource $itemRdf)
    {
        $returnValue = array();

		$impl = $this->getItemModelImplementation($this->getItemModel($itemRdf));
		
		/* @todo reevaluate client side evaluation */
		if ($impl instanceof taoQTI_models_classes_ItemModel) {
				
				$item = null;
				$qtiService = taoQTI_models_classes_QTI_Service::singleton();
				$itemSerial = taoQTI_helpers_qti_ItemAuthoring::getAuthoringItem($itemRdf);
				if(!empty($itemSerial)){//load item from cache
					taoQTI_models_classes_QTI_Data::setPersistence(true);
					$item = $qtiService->getItemBySerial($itemSerial);
				}
				if(is_null($item)){
					$item = $qtiService->getDataItemByRdfItem($itemRdf);
				}
				
				if(!is_null($item)){
					//@todo cache matching data by rdf item
					$returnValue = $item->getMatchingData();
				}else{
					throw new common_Exception('cannot load QTI item for matching data');
				}
		}

        return (array) $returnValue;
    }

    /**
     * Service to evaluate an item
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource itemRdf
     * @param  responses
     * @return array
     */
    public function evaluate( core_kernel_classes_Resource $itemRdf,    $responses)
    {
        $returnValue = array();

		$impl = $this->getItemModelImplementation($this->getItemModel($itemRdf));
		if (in_array('taoItems_models_classes_evaluatableItemModel', class_implements($impl))) {
			$returnValue = $impl->evaluate($itemRdf, $responses);
		} else {
			throw new common_exception_Error('Evaluate called on non-evaluatable item model');
		}

        return (array) $returnValue;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

   		$returnValue = $this->createInstance($clazz);
		if(!is_null($returnValue)){

			$itemFolder = $this->getItemFolder($instance);
			$fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);

			foreach($clazz->getProperties(true) as $property){

				if($property->getUri() == RDFS_TYPE){
					continue;
				}

				$range = $property->getRange();

				if (!is_null($range)) {
					if($range->getUri() == CLASS_GENERIS_FILE){
	
						foreach($instance->getPropertyValuesCollection($property)->getIterator() as $propertyValue){
							$file = new core_kernel_versioning_File($propertyValue->getUri());
							$repo = $file->getRepository();
							$relPath = basename($file->getAbsolutePath());
							if(!empty($relPath)){
								$newPath = tao_helpers_File::concat(array($this->getItemFolder($returnValue), $relPath));
								common_Logger::i('copy '.dirname($file->getAbsolutePath()).' to '.dirname($newPath));
								tao_helpers_File::copy(dirname($file->getAbsolutePath()), dirname($newPath), true);
								if(file_exists($newPath)){
									$subpath = substr($newPath, strlen($repo->getPath()));
									$newFile = $repo->createFile(
										(string)$file->getOnePropertyValue($fileNameProp),
										dirname($subpath).'/'
									);
									$returnValue->setPropertyValue($property, $newFile->getUri());
									$newFile->add(true, true);
									$newFile->commit('Clone of '.$instance->getUri(), true);
								}
							}
						}
					}
					else{
						foreach($instance->getPropertyValues($property) as $propertyValue){
							$returnValue->setPropertyValue($property, $propertyValue);
						}
					}
				} else {
					common_Logger::w('Missing range for property '.$property->getUri());					
				}
			}
			$label = $instance->getLabel();
			$cloneLabel = "$label bis";
			if(preg_match("/bis/", $label)){
				$cloneNumber = (int)preg_replace("/^(.?)*bis/", "", $label);
				$cloneNumber++;
				$cloneLabel = preg_replace("/bis(.?)*$/", "", $label)."bis $cloneNumber" ;
			}

			$returnValue->setLabel($cloneLabel);

			//Measurements
			$measurements = $this->getItemMeasurements($instance);
			if (count($measurements) > 0) {
				$this->setItemMeasurements($returnValue, $measurements);
			}
		}

        return $returnValue;
    }

    /**
     * Short description of method setItemMeasurements
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  array measurements
     * @return taoItems_models_classes_Matching_bool
     */
    public function setItemMeasurements( core_kernel_classes_Resource $item, $measurements)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-5b188be2:135856942ab:-8000:00000000000037CE begin
		$hasMeasurement = new core_kernel_classes_Property(TAO_ITEM_MEASURMENT_PROPERTY);
		$item->removePropertyValues($hasMeasurement);
		foreach ($measurements as $measurement) {
			$measurementres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class(TAO_ITEM_MEASURMENT));
			$measurementPropertiesValues = array(
				TAO_ITEM_IDENTIFIER_PROPERTY		=> $measurement->getIdentifier(),
				TAO_ITEM_DESCRIPTION_PROPERTY		=> $measurement->getDescription(),
				TAO_ITEM_MEASURMENT_HUMAN_ASSISTED	=> $measurement->isHumanAssisted()
					? new core_kernel_classes_Resource(GENERIS_TRUE)
					: new core_kernel_classes_Resource(GENERIS_FALSE)
			);
			if (!is_null($measurement->getScale())) {
				$scaleres = core_kernel_classes_ResourceFactory::create(new core_kernel_classes_Class($measurement->getScale()->getClassUri()));
				$scaleres->setPropertiesValues($measurement->getScale()->toProperties());
				$measurementPropertiesValues[TAO_ITEM_SCALE_PROPERTY] = $scaleres->uriResource;
			}
			$measurementres->setPropertiesValues($measurementPropertiesValues);
			$item->setPropertyValue($hasMeasurement, $measurementres);
		}
		$returnValue = true;

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemMeasurements
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return array
     */
    public function getItemMeasurements( core_kernel_classes_Resource $item)
    {
        $returnValue = array();

		foreach($item->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_MEASURMENT_PROPERTY)) as $uri) {
			$measuremenRessource = new core_kernel_classes_Resource($uri);
			$properties = $measuremenRessource->getPropertiesValues(array(
				new core_kernel_classes_Property(TAO_ITEM_IDENTIFIER_PROPERTY),
				new core_kernel_classes_Property(TAO_ITEM_DESCRIPTION_PROPERTY),
				new core_kernel_classes_Property(TAO_ITEM_SCALE_PROPERTY),
				new core_kernel_classes_Property(TAO_ITEM_MEASURMENT_HUMAN_ASSISTED)
			));
			if (!isset($properties[TAO_ITEM_IDENTIFIER_PROPERTY])) {
				throw new common_exception_Error('Missing identifier for Measurement');
			}
			$identifier = (string)array_pop($properties[TAO_ITEM_IDENTIFIER_PROPERTY]);
			if (isset($properties[TAO_ITEM_SCALE_PROPERTY])) {
				$scale = taoItems_models_classes_Scale_Scale::buildFromRessource(array_pop($properties[TAO_ITEM_SCALE_PROPERTY]));
			}
			$desc = '';
			if (isset($properties[TAO_ITEM_DESCRIPTION_PROPERTY])) {
				foreach ($properties[TAO_ITEM_DESCRIPTION_PROPERTY] as $subdesc) {
					$desc .= $subdesc;
				}
			}
			$returnValue[$identifier] = new taoItems_models_classes_Measurement($identifier, $desc);
			if (isset($scale) && !is_null($scale)) {
				$returnValue[$identifier]->setScale($scale);
			}
			if (isset($properties[TAO_ITEM_MEASURMENT_HUMAN_ASSISTED])) {
				$assisted = array_pop($properties[TAO_ITEM_MEASURMENT_HUMAN_ASSISTED]);
				$returnValue[$identifier]->setHumanAssisted($assisted->getUri() == GENERIS_TRUE);
			}
		}

        return (array) $returnValue;
    }

    /**
     * Short description of method getItemModel
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return core_kernel_classes_Resource
     */
    public function getItemModel( core_kernel_classes_Resource $item)
    {
        $returnValue = null;

		$itemModel = $item->getOnePropertyValue($this->itemModelProperty);
		if($itemModel instanceof core_kernel_classes_Resource){
			$returnValue = $itemModel;
		}

        return $returnValue;
    }

    /**
     * Rertrieve current user's language from the session object to know where
     * item content should be located
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getSessionLg()
    {
        $returnValue = (string) '';

		$session = core_kernel_classes_Session::singleton();
		if ($session->getDataLanguage() != '') {
			$returnValue = $session->getDataLanguage();
		} else {
			throw new Exception('the data language of the user cannot be found in session');
		}

        return (string) $returnValue;
    }

    /**
     * Short description of method deleteItemContent
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function deleteItemContent( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

        //delete the folder for all languages!
		foreach($item->getUsedLanguages($this->itemContentProperty) as $lang){
			$files = $item->getPropertyValuesByLg($this->itemContentProperty, $lang);
			foreach($files->getIterator() as $file){
				$file = new core_kernel_classes_File($file);
				if (core_kernel_versioning_File::isVersionedFile($file)) {
					$file = new core_kernel_versioning_File($file);
				}
				try{
					$file->delete();
				} catch (core_kernel_versioning_exception_FileUnversionedException $e) {
					// file was not versioned after all, ignore in delte
				}
			}
			
		}

		$returnValue = true;

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemModelImplementation
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource itemModel
     * @return taoItems_models_classes_itemModel
     */
    public function getItemModelImplementation( core_kernel_classes_Resource $itemModel)
    {
        $returnValue = null;

		$services = $itemModel->getPropertyValues(new core_kernel_classes_Property(PROPERTY_ITEM_MODEL_SERVICE));
		if (count($services) > 0) {
			if (count($services) > 1) {
				throw new common_exception_Error('Conflicting services for itemmodel '.$itemModel->getLabel());
			}
			$serviceName = (string)current($services);
			if (class_exists($serviceName) && in_array('taoItems_models_classes_itemModel', class_implements($serviceName))) {
				$returnValue = new $serviceName();
			} else {
				throw new common_exception_Error('Item model service '.$serviceName.' not found, or not compatible for item model '.$itemModel->getLabel());
			}
		} else {
			common_Logger::d('No implementation for '.$itemModel->getLabel());
		}

        return $returnValue;
    }

    /**
     * Short description of method isItemVersioned
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @return boolean
     */
    public function isItemVersioned( core_kernel_classes_Resource $item)
    {
        $returnValue = (bool) false;

		$files = $item->getPropertyValues($this->itemContentProperty);
		foreach ($files as $file) {
			// theoreticaly this should always be no or a single file 
			if ($file->hasType(new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE))) {
				$returnValue = true;
			}
		}

        return (bool) $returnValue;
    }

    /**
     * Short description of method getItemFolder
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Resource item
     * @param  string lang
     * @return string
     */
    public function getItemFolder( core_kernel_classes_Resource $item, $lang = '')
    {
        $returnValue = (string) '';

		if ($lang === '') {
			$files = $item->getPropertyValues(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY));
		} else {
			$files = $item->getPropertyValuesByLg(new core_kernel_classes_Property(TAO_ITEM_CONTENT_PROPERTY), $lang)->toArray();
		}
		if (count($files) == 0) {
			// no content found assign default
			$returnValue = $this->getDefaultItemFolder($item, $lang);
		} else {
			if (count($files) > 1) {
				throw new common_Exception(__METHOD__.': Item '.$item->getUri().' has multiple.');
			}
			$content = new core_kernel_classes_File(current($files));
			$returnValue = dirname($content->getAbsolutePath()).DIRECTORY_SEPARATOR;
		}

        return (string) $returnValue;
    }

    /**
     * sets the filesource to use for new items
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  Repository filesource
     * @return mixed
     */
    public function setDefaultFilesource( core_kernel_versioning_Repository $filesource)
    {
		$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
        $ext->setConfig(self::CONFIG_DEFAULT_FILESOURCE, $filesource->getUri());
    }

    /**
     * returns the filesource to use for new items
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_versioning_Repository
     */
    public function getDefaultFileSource()
    {
        $returnValue = null;

    	$ext = common_ext_ExtensionsManager::singleton()->getExtensionById('taoItems');
		$uri = $ext->getConfig(self::CONFIG_DEFAULT_FILESOURCE);
		if (!empty($uri)) {
			$returnValue = new core_kernel_versioning_Repository($uri);
		} else {
			throw new common_Exception('No default repository defined for Items storage.');
		}

        return $returnValue;
    }

} /* end of class taoItems_models_classes_ItemsService */

?>