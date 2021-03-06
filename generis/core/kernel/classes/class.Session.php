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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */


/**
 * Represents a Session on Generis.
 *
 * @access private
 * @author patrick@taotesting.com
 * @package generis
 * @subpackage core_kernel_classes
 */
class core_kernel_classes_Session
{

    /**
     * The single instance of core_kernel_classes_Session
     *
     * @access private
     * @var Session
     */
    private static $instance = null;

    /**
     * The language in use to access data stored into persistent memory for
     * the currenly authenticated user.
     *
     * @access private
     * @var string
     */
    private $dataLanguage = '';

    /**
     * The language in use at the gui level for the currently authenticated user.
     *
     * @access private
     * @var string
     */
    private $interfaceLanguage = '';

    /**
     * The login of the currently authenticated user.
     *
     * @access private
     * @var string
     */
    private $userLogin = '';

    /**
     * the URI identifying the currently authenticated user in persistent memory.
     *
     * @access private
     * @var string
     */
    private $userUri = '';

    /**
     * The default language to use if the data language or GUI language
     * cannot be accessed. It is used as a fallback language.
     *
     * @access public
     * @var string
     */
    public $defaultLg = '';

    /**
     * The RDF models currently loaded for the authenticated user. This associative array
     * contains keys that are model IDs and values are URIs as strings.
     *
     * @access protected
     * @var array
     */
    protected $loadedModels = array();

    /**
     * The models that can be updated (modified) by the currently authenticated
     * user. This associative array contains keys that are model IDs and values 
     * are URIs as strings.
     *
     * @access protected
     * @var array
     */
    protected $updatableModels = array();

    /**
     * The roles that the currently authenticated user is given. This array
     * contains instances of core_kernel_classes_Resource.
     *
     * @access private
     * @var array
     */
    private $userRoles = array();


    /**
     * Obtain a single core_kernel_classes_Session instance.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return core_kernel_classes_Session
     */
    public static function singleton()
    {
        $returnValue = null;

        $session = PHPSession::singleton();
        
		if (!isset(self::$instance) || is_null(self::$instance)) {
			if ($session->hasAttribute('generis_session')) {
				self::$instance = $session->getAttribute('generis_session');
			} else {
				self::$instance = new self();
				$session->setAttribute('generis_session', self::$instance);
			}
		}
		$returnValue = self::$instance;

        return $returnValue;
    }

    /**
     * This function is used to reset the session values.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return void
     */
    public function reset()
    {
		common_Logger::d('resetting session');
		$this->defaultLg = DEFAULT_LANG;
		$this->setDataLanguage('');
		$this->setInterfaceLanguage('');

		$this->userLogin	= '';
		$this->userUri		= null;
		$this->userRoles	= array();
		$this->update();
    }

    /**
     * Creates a new instance of core_kernel_classes_Session
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     */
    private function __construct()
    {
		
		//active  models needed by extension
    	$extensionManager = common_ext_ExtensionsManager::singleton();
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->loadModel($model);
		}
		
		//load local model
		$this->loadModel(LOCAL_NAMESPACE);
		
		//get updatable models
		$this->updatableModels = $extensionManager->getUpdatableModels ();
		
		//set default language
		$this->defaultLg			= DEFAULT_LANG;
		$this->interfaceLanguage	= '';
		$this->dataLanguage			= '';
    }

    /**
     * Please use setDataLanguage() instead.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @deprecated
     * @param  string lg
     * @return boolean
     */
    public function setLg($lg)
    {
        $returnValue = (bool) false;

        $returnValue = $this->setDataLanguage($lg);

        return (bool) $returnValue;
    }

    /**
     * Please use getDataLanguage() instead.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @deprecated
     * @return string
     */
    public function getLg()
    {
        $returnValue = (string) '';

        $returnValue = $this->getDataLanguage();

        return (string) $returnValue;
    }

    /**
     * Get the local namespace (model) in use by the authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getNameSpace()
    {
        $returnValue = (string) '';

		$returnValue= LOCAL_NAMESPACE;

        return (string) $returnValue;
    }

    /**
     *Returns the login of the currently authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getUserLogin()
    {
        $returnValue = (string) '';

		$returnValue = $this->userLogin;

        return (string) $returnValue;
    }

    /**
     * Get the URI identifying the currently authenticated user in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getUserUri()
    {
        $returnValue = (string) '';

        return $this->userUri;

        return (string) $returnValue;
    }

    /**
     * Set the currently authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string login The login of the user.
     * @param  string uri The URI identifying the user in persistent memory.
     * @param  array roles The array of roles that the user is given. This array
     */
    public function setUser($login, $uri = null, $roles = array())
    {
    	$this->userLogin	= $login;
    	$this->userUri		= $uri;
    	$this->userRoles	= $roles;
    }

    /**
     * Load a particular model depending on the provided URI.
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string model A URI.
     * @return boolean true if the model was correctly loaded, false otherwise.
     */
    protected function loadModel($model)
    {
        $returnValue = (bool) false;

        
        if(!preg_match("/#$/", $model)){
        	$model .= '#';
        }
        if(in_array($model, $this->loadedModels)){
        	$returnValue = true;
        }
        else{
        	$nsManager = common_ext_NamespaceManager::singleton();
        	foreach($nsManager->getAllNamespaces() as $namespace){
        		if($namespace->getUri() == $model){
        			$this->loadedModels[$namespace->getModelId()] = $model;
        			$returnValue = true;
        			break;
        		}
        	}
        }

        return (bool) $returnValue;
    }

    /**
     * Obtain the list of the models loaded for the authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array An array of URIs as strings
     */
    public function getLoadedModels()
    {
        $returnValue = array();
        
        $returnValue = $this->loadedModels;

        return (array) $returnValue;
    }

    /**
     * Get the list of models that are updatable (editable) by the currently
     * authenticated user.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array An associative array where keys Uare integers and values are URI strings.
     */
    public function getUpdatableModels()
    {
        $returnValue = array();
        
        $returnValue = $this->updatableModels;        

        return (array) $returnValue;
    }
    
    /**
     * Set the list of models that are updatable by the currently authenticated
     * user.
     * 
     * @param array $updatableModels An associative array where keys Uare integers and values are URI strings.
     */
    public function setUpdatableModels(array $updatableModels)
    {
    	$this->updatableModels = $updatableModels;
    }

    /**
     * Unload a model from the current session.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string model The model URI.
     * @return boolean
     */
    public function unloadModel($model)
    {
        $returnValue = (bool) false;

        foreach ($this->loadedModels as $loadedModel){
        	if ($loadedModel == $model){
        		unset($loadedModel);
        		$returnValue = true;
        		break;
        	}
        }

        return (bool) $returnValue;
    }

    /**
     * Updates the session by reloading references to models.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     */
    public function update()
    {
        $this->loadedModels = array();
        $extensionManager = common_ext_ExtensionsManager::singleton();
        common_ext_NamespaceManager::singleton()->reset();
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->loadModel($model);
		}
		
		//load local model
		$this->loadModel(LOCAL_NAMESPACE);
		
		//get updatable models
		$this->updatableModels = array();
		$this->updatableModels = $extensionManager->getUpdatableModels ();
    }

    /**
     * Behaviour to adopt at PHP __wakup time.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return void
     */
    public function __wakeup()
    {
        $this->update();
    }

    /**
     * Set the language to use for data access in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string language
     */
    public function setDataLanguage($language)
    {
        if (empty($language)) {
        	$this->dataLanguage = '';
        } else {
        	$this->dataLanguage = $language;
        }
	    common_Logger::d('Set data language to '.$language, array('GENERIS', 'I18N'));
    }

    /**
     * Obtain the language to use for data access in persistent memory.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getDataLanguage()
    {
        $returnValue = (string) '';

        $returnValue = empty($this->dataLanguage) ? $this->defaultLg : $this->dataLanguage;

        return (string) $returnValue;
    }

    /**
     * Change the language to use at the GUI level.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string language
     */
    public function setInterfaceLanguage($language)
    {
		if (empty($language)) {
        	$this->interfaceLanguage = '';
        } else {
        	$this->interfaceLanguage = $language;
        }
    }

    /**
     * returns the language code associated with user interactions
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public function getInterfaceLanguage()
    {
        $returnValue = (string) '';

		$returnValue = empty($this->interfaceLanguage) ? $this->defaultLg : $this->interfaceLanguage;

        return (string) $returnValue;
    }

    /**
     * returns the roles of the current user
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return array An array of core_kernel_classes_Resource
     */
    public function getUserRoles()
    {
        $returnValue = array();

        return $this->userRoles;

        return (array) $returnValue;
    }

}

?>