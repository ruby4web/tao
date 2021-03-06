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
if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

class ltiProvider_models_classes_LtiUtils
{
	const LIS_CONTEXT_ROLE_NAMESPACE = 'urn:lti:role:ims/lis/';
	
	public static function mapLTIRole2TaoRole($role) {
		$taoRole = null;
		if(filter_var($role, FILTER_VALIDATE_URL)){
			// url found
			$taoRole = new core_kernel_classes_Resource($role);
		} else {
			// if not fully qualified prepend LIS context role NS
			if (strtolower(substr($role, 0, 4)) !== 'urn:') {
				$role = self::LIS_CONTEXT_ROLE_NAMESPACE.$role;
			}
			list($prefix, $nid, $nss) = explode(':', $role, 3);
			if ($nid != 'lti') {
				throw new common_Exception('Non LTI URN '.$role.' not supported');
			}
			$urn = 'urn:'.strtolower($nid).':'.$nss;
			
			// search for fitting role
			$class = new core_kernel_classes_Class(CLASS_LTI_ROLES);
			$cand = $class->searchInstances(array(
				PROPERTY_LTI_ROLES_URN => $urn
			));
			if (count($cand) > 1) {
				throw new common_exception_Error('Multiple instances share the URN '.$urn);
			}
			if (count($cand) == 1) {
				$taoRole = current($cand); 
			} else {
				common_Logger::w('Unknown LTI role with urn: '.$urn);
			}
		}
		if (!is_null($taoRole) && $taoRole->exists()) {
			return $taoRole;
		} else {
			return null;
		}
	}
	
	public static function mapCode2InterfaceLanguage($code) {
		$returnValue = tao_models_classes_LanguageService::singleton()->getLanguageByCode(DEFAULT_LANG);
		return $returnValue;
	}
}

?>