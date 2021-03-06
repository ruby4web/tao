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
?>
<?php 

core_control_FrontController::connect(SYS_USER_LOGIN, SYS_USER_PASS, DATABASE_NAME);
error_reporting(E_ALL);

$dbWarpper = core_kernel_classes_DbWrapper::singleton();

$generisUserClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
$classRole = new core_kernel_classes_Class(CLASS_ROLE);
$classTaoManager = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#TaoManagerRole');





$loginProp = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
$passProp = new core_kernel_classes_Property(PROPERTY_USER_PASSWORD);
$defLgProp = new core_kernel_classes_Property(PROPERTY_USER_DEFLG);
$firstNameProp = new core_kernel_classes_Property(PROPERTY_USER_FIRTNAME);
$mailProp = new core_kernel_classes_Property(PROPERTY_USER_MAIL);
$lastNameProp = new core_kernel_classes_Property(PROPERTY_USER_LASTNAME);
$uiLgProp = new core_kernel_classes_Property(PROPERTY_USER_UILG);

//Migrate previous backoffice user
$result = $dbWarpper->execSql('select * from user');
while (!$result-> EOF){
	$newLogin = $result->fields['login'];
	if(!core_kernel_users_Service::singleton()->loginExists($newLogin)) {
		$newUserInstance = $classTaoManager->createInstance('User_'.$result->fields['login'],'Generated during update from user table on'. date(DATE_ISO8601));
		echo 'Migrate Manager '. 'User_'.$result->fields['login'] . '<br/>';
		$newUserInstance->setPropertyValue($loginProp,$result->fields['login']);
		$newUserInstance->setPropertyValue($passProp,$result->fields['password']);
		$newUserInstance->setPropertyValue($lastNameProp,$result->fields['LastName']);
		$newUserInstance->setPropertyValue($firstNameProp,$result->fields['FirstName']);
		$newUserInstance->setPropertyValue($mailProp,$result->fields['E_Mail']);
		$newUserInstance->setPropertyValue($defLgProp,'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.strtoupper($result->fields['Deflg']));
		$newUserInstance->setPropertyValue($uiLgProp, 'http://www.tao.lu/Ontologies/TAO.rdf#Lang'.strtoupper($result->fields['Uilg']));
	}
	$result->MoveNext();
}


//Migrate previous Subject user
$taoSubjectClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject');
$subjectInstancesArray = $taoSubjectClass->getInstances(true);
$subjectLoginProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOSubject.rdf#Login');
$subjectPassProp = new core_kernel_classes_Property('http://www.tao.lu/Ontologies/TAOSubject.rdf#Password');

foreach ($subjectInstancesArray as $subject) {
	$newLogin = $subject->getOnePropertyValue($subjectLoginProp);
	$newPass = $subject->getOnePropertyValue($subjectPassProp);
	error_reporting(E_ALL);
	if(!core_kernel_users_Service::singleton()->loginExists($newLogin)) {
		echo 'Migrate Subject '. $newLogin . '<br/>';
		$subject->editPropertyValues($loginProp,$newLogin);
		$subject->editPropertyValues($passProp,md5($newPass));
		$subject->removePropertyValues($subjectLoginProp);
		$subject->removePropertyValues($subjectPassProp);
	}

}
$result = $dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='http://www.tao.lu/Ontologies/TAOSubject.rdf#Login' OR
 `subject` ='http://www.tao.lu/Ontologies/TAOSubject.rdf#Password';");


$wfUserClass =  new core_kernel_classes_Class('http://www.tao.lu/middleware/taoqual.rdf#i11859665003194');
$wfUserInstancesArray = $wfUserClass->getInstances(true);
$wfUserLoginProp = new core_kernel_classes_Property('http://www.tao.lu/middleware/taoqual.rdf#i119012256329986');
$wfUserPassProp = new core_kernel_classes_Property('http://www.tao.lu/middleware/taoqual.rdf#i119012711429320');
$wfUserMailProp = new core_kernel_classes_Property('http://www.tao.lu/middleware/taoqual.rdf#i120593879614028');
$wfUserRoleProp = new core_kernel_classes_Property('http://www.tao.lu/middleware/taoqual.rdf#i119012169222836');

$backOfficeClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#BackOffice');
$wfRoleTopClass = new core_kernel_classes_Class('http://www.tao.lu/Ontologies/TAO.rdf#WorkflowUser');
	


foreach($wfUserInstancesArray as $wfUser){

	$role = $wfUser->getOnePropertyValue($wfUserRoleProp); 

	if($role instanceof core_kernel_classes_Resource) {
		$role->setPropertyValue(new core_kernel_classes_Property(RDF_TYPE), $wfRoleTopClass->uriResource);
	
		$role->editPropertyValues(new core_kernel_classes_Property(RDF_SUBCLASSOF),CLASS_GENERIS_USER);
		
		$roleClass = new core_kernel_classes_Class($role->uriResource);
		$newLogin = $wfUser->getOnePropertyValue($wfUserLoginProp); 
		if(!core_kernel_users_Service::singleton()->loginExists($newLogin)) {
			echo 'Migrate wfUser '. $newLogin . '<br/>';
			$newPass = $wfUser->getOnePropertyValue($wfUserPassProp); 	
			$newMail =  $wfUser->getOnePropertyValue($wfUserMailProp); 	
			$newWfUserInstance = $roleClass->createInstance('wfUser_'. $newLogin,'Generated during update from user table on'. date(DATE_ISO8601));
			$newWfUserInstance->setPropertyValue($loginProp,$newLogin);
			$newWfUserInstance->setPropertyValue($passProp,md5($newPass));
			$newUserInstance->setPropertyValue($mailProp,$newMail);
		}
		else{
			
		}
	}
	$wfUser->delete();
	
	$result = $dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUser->uriResource. "';");

}
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserClass->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserLoginProp->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserPassProp->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserClass->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserMailProp->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserRoleProp->uriResource. "';");
$dbWarpper->execSql("DELETE FROM `statements` WHERE `subject` ='". $wfUserRoleProp->uriResource. "';");


echo 'done';
?>