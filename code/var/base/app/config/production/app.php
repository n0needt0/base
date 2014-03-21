<?php

return array(
/************************************************************/
/*******************APP SPECIFIC***************************/
'copyright' => 'Helppain.net',

'emdtobilltrac'=>array(
    'alpha_owner'=>array(
                            array('start'=>'a', 'end'=>'z', 'owner'=>'jalvarado')
                    ),
    'insurance_owner'=>array(
                            array('pattern'=>'liberty', 'owner'=>'ktrylovich ')
                    ),
    'db_write' => true,
    'db_delete'=>false,
                ),

'locationmap'=>array(
                'Help Pain Fresno'=>'fresno',
                'Help Pain Medical Network'=>'san mateo',
                'Help Pain Salinas'=>'salinas'
                ),

'validservices'=>array(
                'consult'=>array(),
                'detox'=>array('note'=>'Detox IN', 'price_check'=>array('operator'=>'mod', 'value'=>2000)),
                'evaluation'=>array(),
                'follow Up'=>array(),
                'help remote care'=>array(),
                'program'=>array(),
                'refill'=>array(),
                'other'=>array(),
                'letter'=>array(),
                'injection'=>array(),
                'follow up'=>array(),
                'hospital (SCIPP or MILLS or Stanford)'=>array('note'=>'SCIPP or MILLS or Stanford', 'price_check'=>array('operator'=>'mod', 'value'=>2000)),
                'reassessment'=>array(),
                'UDS Report'=>array(),
                'WC f/up'=>array(),
                'CMA Visit'=>array(),
                'PVT f/up'=>array(),
                'permanent & stationary report'=>array()
                ),

'emails' =>array(
                            'admin'=>'ayasinsky@helppain.net',
                            'billing'=>array('sglenn@helppain.net', 'jcarey@helppain.net','shollins@helppain.net')
                                           ),

'emdtonetsuite'=>array(
                                        'subsidiary'=>7, //helppain
                                        'defaultreceivables'=>408,
                                        'hardstoponerrors'=>false,

                                        'invoice'=>array('account'=>351,
                                                                   'taxItem'=>-7,
                                                                   'subsidiary'=>7,
                                                                   'prefix'=>'EMD',
                                                                   'price'=>-1        //custom
                                                        ),
                                         'location'=>array(
                                                                     'HPF'=>16, //fresno
                                                                     'HPPM'=>27, //san mateo
                                                                     'HPS'=>24, //salinas
                                                                     'catchall'=>27 //TODO
                                                         ),

                                        'servicemap'=>array(
                                                                    "consult"=>(object)array("internalId"=>58),
                                                                    "evaluation"=>(object)array("internalId"=>57),
                                                                    "detox"=>(object)array("internalId"=>56),
                                                                    "remote"=>(object)array("internalId"=>60),
                                                                    "program"=>(object)array("internalId"=>59),
                                                                    "interest"=>(object)array("internalId"=>864),
                                                                    "discount"=>(object)array("internalId"=>863),
                                                                    "catchall"=>(object)array("internalId"=>61)
                                                        ),

                                        'customfields'=>array(
                                                                    "provider"=>'custbody4',
                                                                    "affiliate"=>'custbody5'
                                                        ),

                                        'payment'=>array(
                                                                   "araccount"=>351, //11010 AR
                                                                   "depositaccount"=>338,
                                                                   "undeposit"=>true //keep deposits in undeposited funds account
                                                        ),

                                        'error_email'=>false //turns off debug emails
                                ),

/*
* Application name will be used in emails, title, etc
*/
'app_name' => 'Base',
/*
* Jquery Mobile Settings
*/
'jqm_theme'=>'b',
'jqm_theme_alt'=>'e',

/*
* Where to output java script debug
*     false - none
*     debugger - starts debugger
*     console - console
*/

'jsdebug' => 'false',
'debug_json' => true,

	/*
	|--------------------------------------------------------------------------
	| Application Debug Mode
	|--------------------------------------------------------------------------
	|
	| When your application is in debug mode, detailed error messages with
	| stack traces will be shown on every error that occurs within your
	| application. If disabled, a simple generic error page is shown.
	|
	*/

	'debug' => true,

	/*
	|--------------------------------------------------------------------------
	| Application URL
	|--------------------------------------------------------------------------
	|
	| This URL is used by the console to properly generate URLs when using
	| the Artisan command line tool. You should set this to the root of
	| your application so that it is used when running Artisan tasks.
	|
	*/

	'url' => 'https://base.helppain.net',

	/*
	|--------------------------------------------------------------------------
	| Application Timezone
	|--------------------------------------------------------------------------
	|
	| Here you may specify the default timezone for your application, which
	| will be used by the PHP date and date-time functions. We have gone
	| ahead and set this to a sensible default for you out of the box.
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| This key is used by the Illuminate encrypter service and should be set
	| to a random, long string, otherwise these encrypted values will not
	| be safe. Make sure to change it before deploying any application!
	|
	*/

	'key' => 'nkBlz8KjTZ3AfiWD6Vl4Gm9gERWayz9m',

	/*
	|--------------------------------------------------------------------------
	| Autoloaded Service Providers
	|--------------------------------------------------------------------------
	|
	| The service providers listed here will be automatically loaded on the
	| request to your application. Feel free to add your own services to
	| this array to grant expanded functionality to your applications.
	|
	*/

	'providers' => array(

		'Illuminate\Foundation\Providers\ArtisanServiceProvider',
                'Illuminate\Auth\AuthServiceProvider',
                'Illuminate\Cache\CacheServiceProvider',
                'Illuminate\Session\CommandsServiceProvider',
                'Illuminate\Foundation\Providers\ConsoleSupportServiceProvider',
                'Illuminate\Routing\ControllerServiceProvider',
                'Illuminate\Cookie\CookieServiceProvider',
                'Illuminate\Database\DatabaseServiceProvider',
                'Illuminate\Encryption\EncryptionServiceProvider',
                'Illuminate\Filesystem\FilesystemServiceProvider',
                'Illuminate\Hashing\HashServiceProvider',
                'Illuminate\Html\HtmlServiceProvider',
                'Illuminate\Log\LogServiceProvider',
                'Illuminate\Mail\MailServiceProvider',
                'Illuminate\Database\MigrationServiceProvider',
                'Illuminate\Pagination\PaginationServiceProvider',
                'Illuminate\Queue\QueueServiceProvider',
                'Illuminate\Redis\RedisServiceProvider',
                'Illuminate\Remote\RemoteServiceProvider',
                'Illuminate\Auth\Reminders\ReminderServiceProvider',
                'Illuminate\Database\SeedServiceProvider',
                'Illuminate\Session\SessionServiceProvider',
                'Illuminate\Translation\TranslationServiceProvider',
                'Illuminate\Validation\ValidationServiceProvider',
                'Illuminate\View\ViewServiceProvider',
                'Illuminate\Workbench\WorkbenchServiceProvider',
		        'Cartalyst\Sentry\SentryServiceProvider',
		        'Lj4\RedbeanLaravel4\RedbeanLaravel4ServiceProvider'
	),

	/*
	|--------------------------------------------------------------------------
	| Service Provider Manifest
	|--------------------------------------------------------------------------
	|
	| The service provider manifest is used by Laravel to lazy load service
	| providers which are not needed for each request, as well to keep a
	| list of all of the services. Here, you may set its storage spot.
	|
	*/

	'manifest' => storage_path().'/meta',

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	|
	| This array of class aliases will be registered when this application
	| is started. However, feel free to register as many as you wish as
	| the aliases are "lazy" loaded so they don't hinder performance.
	|
	*/

	'aliases' => array(

		 'App' => 'Illuminate\Support\Facades\App',
                'Artisan' => 'Illuminate\Support\Facades\Artisan',
                'Auth' => 'Illuminate\Support\Facades\Auth',
                'Blade' => 'Illuminate\Support\Facades\Blade',
                'Cache' => 'Illuminate\Support\Facades\Cache',
                'ClassLoader' => 'Illuminate\Support\ClassLoader',
                'Config' => 'Illuminate\Support\Facades\Config',
                'Controller' => 'Illuminate\Routing\Controller',
                'Cookie' => 'Illuminate\Support\Facades\Cookie',
                'Crypt' => 'Illuminate\Support\Facades\Crypt',
                'DB' => 'Illuminate\Support\Facades\DB',
                'Eloquent' => 'Illuminate\Database\Eloquent\Model',
                'Event' => 'Illuminate\Support\Facades\Event',
                'File' => 'Illuminate\Support\Facades\File',
                'Form' => 'Illuminate\Support\Facades\Form',
                'Hash' => 'Illuminate\Support\Facades\Hash',
                'HTML' => 'Illuminate\Support\Facades\HTML',
                'Input' => 'Illuminate\Support\Facades\Input',
                'Lang' => 'Illuminate\Support\Facades\Lang',
                'Log' => 'Illuminate\Support\Facades\Log',
                'Mail' => 'Illuminate\Support\Facades\Mail',
                'Paginator' => 'Illuminate\Support\Facades\Paginator',
                'Password' => 'Illuminate\Support\Facades\Password',
                'Queue' => 'Illuminate\Support\Facades\Queue',
                'Redirect' => 'Illuminate\Support\Facades\Redirect',
                'Redis' => 'Illuminate\Support\Facades\Redis',
                'Request' => 'Illuminate\Support\Facades\Request',
                'Response' => 'Illuminate\Support\Facades\Response',
                'Route' => 'Illuminate\Support\Facades\Route',
                'Schema' => 'Illuminate\Support\Facades\Schema',
                'Seeder' => 'Illuminate\Database\Seeder',
                'Session' => 'Illuminate\Support\Facades\Session',
                'SSH' => 'Illuminate\Support\Facades\SSH',
                'Str' => 'Illuminate\Support\Str',
                'URL' => 'Illuminate\Support\Facades\URL',
                'Validator' => 'Illuminate\Support\Facades\Validator',
                'View' => 'Illuminate\Support\Facades\View',
		        'Sentry'		  => 'Cartalyst\Sentry\Facades\Laravel\Sentry',
		        'R' => 'Redbean\R',
	),

);
