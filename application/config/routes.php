<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'account';

// Business Settings Mobule

/*$route['settings/manoj/services'] = 'services/$1';

$route['settings/business/index'] = 'business/$1';
$route['settings/business/details/(:any)/(:any)/(:any)'] = 'business/details/$1/$1';
$route['settings/business/calendar_settings'] = 'business/add_services/$1/$1';
$route['settings/business/online_booking'] = 'business/add_services/$1/$1';
$route['settings/business/addons/(:any)/(:any)/(:any)'] = 'business/add_services/$1/$1';
$route['settings/business/locations/(:any)/(:any)/(:any)'] = 'business/locations/$1/$1';
$route['settings/business/staff/(:any)/(:any)/(:any)'] = 'business/staff/$1/$1';


$route['settings/business/classes/(:any)/(:any)/(:any)'] = 'services/add_services/$1/$1';
$route['settings/business/resources/(:any)/(:any)/(:any)'] = 'services/add_services/$1/$1';


// Salestool Settings Mobule
$route['settings/salestool'] = 'salestool/$1';
$route['settings/salestool/discounts/(:any)/(:any)/(:any)'] = 'salestool/discounts/$1/$1';
$route['settings/salestool/packages/(:any)/(:any)/(:any)'] = 'salestool/packages/$1/$1';
$route['settings/salestool/gift_vouchers/(:any)/(:any)/(:any)'] = 'salestool/gift_vouchers/$1/$1';
$route['settings/salestool/invoice_taxes/(:any)/(:any)/(:any)'] = 'salestool/invoice_taxes/$1/$1';
$route['settings/salestool/products/(:any)/(:any)/(:any)'] = 'salestool/products/$1/$1';

// Administration Settings Mobule
$route['settings/administration'] = 'administration/$1';
$route['settings/administration/administrators/(:any)/(:any)/(:any)'] = 'administration/administrators/$1/$1';
$route['settings/administration/close_dates/(:any)/(:any)/(:any)'] = 'administration/close_dates/$1/$1';
$route['settings/administration/roster/(:any)/(:any)/(:any)'] = 'administration/roster/$1/$1';

// Notifications Settings Mobule
$route['settings/notification'] = 'notification/$1';
$route['settings/notification/email_settings/(:any)/(:any)/(:any)'] = 'notification/email_settings/$1/$1';
$route['settings/notification/SMS_settings/(:any)/(:any)/(:any)'] = 'notification/SMS_settings/$1/$1';
$route['settings/notification/staff_notifications/(:any)/(:any)/(:any)'] = 'notification/staff_notifications/$1/$1';
$route['settings/notification/patient_reminders/(:any)/(:any)/(:any)'] = 'notification/patient_reminders/$1/$1';

// Promote Settings Mobule
$route['settings/promote'] = 'promote/$1';
$route['settings/promote/booking_buttons/(:any)/(:any)/(:any)'] = 'promote/booking_buttons/$1/$1';
$route['settings/promote/mini_website/(:any)/(:any)/(:any)'] = 'promote/mini_website/$1/$1';
$route['settings/promote/facebook_app/(:any)/(:any)/(:any)'] = 'promote/facebook_app/$1/$1';
$route['settings/promote/google_analytics/(:any)/(:any)/(:any)'] = 'promote/google_analytics/$1/$1';
*/

$route['^en/(.+)$'] = "$1";
$route['^hi/(.+)$'] = "$1";
$route['^(\w{2})$'] = $route['default_controller'];
$route['^(\w{2})/(.+)$'] = "$2";

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
