<?php
$manifest = array(

	'acceptable_sugar_versions' => array (
		'regex_matches' => array (
			0 => "6\.*\.*",
		),
	),
	'acceptable_sugar_flavors' => array (
		0 => 'CE',
		1 => 'PRO',
		2 => 'ENT'
	),
	'name' 				=> 'Activities Reports',
	'description' 		=> 'Activities Reports',
	'author' 			=> 'SugarCRM.',
	'published_date'	=> '2011-10-28',
	'version' 			=> '1.2',
	'type' 				=> 'module',
	'icon' 				=> '',
	'is_uninstallable' => true,
);

$installdefs = array(
	'id'=> 'ActivitesReports',

	'copy' => array(

		array('from'=> '<basepath>/ActivitiesReports.php',
			  'to'=> 'modules/Calendar/ActivitiesReports.php',
		),
		array('from'=> '<basepath>/ActivitiesReports.tpl',
			  'to'=> 'modules/Calendar/ActivitiesReports.tpl',
		),
		array('from'=> '<basepath>/activitiesReports.js',
			  'to'=> 'include/javascript/activitiesReports.js',
		),
	),
	'language' => array (
		array('from'=> '<basepath>/en_us.activities_reports.php',
			'to_module'=> 'Calendar',
			'language'=>'en_us',
		),
	),
	'menu' => array (
		array(
			'from'=> '<basepath>/new_activities_menu.ext.php',
			'to_module'=> 'Calendar',
			'language'=>'en_us',
		),
	),

);
?>
