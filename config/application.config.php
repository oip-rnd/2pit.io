<?php
return array(
    // This should be an array of module namespaces used in the application.
    'modules' => array(
    	'PpitCore',
    	'PpitCommitment',
    	'PpitContact',
       	'PpitAccounting',
//    	'PpitExpense',
    	'PpitFlow',
    	'PpitLearning',
    	'PpitStudies',
    	'PpitUser',
    	'Application',
    	'Cclam',
    	'Esi',
    	'Mantesalo',
    	'P_Pit',
    	'Sea',
    ),

    // These are various options for the listeners attached to the ModuleManager
    'module_listener_options' => array(
        // This should be an array of paths in which modules reside.
        // If a string key is provided, the listener will consider that a module
        // namespace, the value of that key the specific path to that module's
        // Module class.
        'module_paths' => array(
            './module',
            './vendor','./module',
        ),

        // An array of paths from which to glob configuration files after
        // modules are loaded. These effectively override configuration
        // provided by modules themselves. Paths may use GLOB_BRACE notation.
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
            'config/autoload/innocoins/{,*.}{global,local}.php',
//            'config/autoload/2pit.io/{,*.}{global,local}.php',
//            'config/autoload/cclam.p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/esi.p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/flowux.io/{,*.}{global,local}.php',
//            'config/autoload/mantesalo.p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/mjconseil.p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/pc-x-u.com/{,*.}{global,local}.php',
//			'config/autoload/sea.p-pit.fr/{,*.}{global,local}.php',
//            'config/autoload/synapps-hum-ibfs.socgen.com/{,*.}{global,local}.php',
        ),

        // Whether or not to enable a configuration cache.
        // If enabled, the merged configuration will be cached and used in
        // subsequent requests.
        //'config_cache_enabled' => $booleanValue,

        // The key used to create the configuration cache file name.
        //'config_cache_key' => $stringKey,

        // Whether or not to enable a module class map cache.
        // If enabled, creates a module class map cache which will be used
        // by in future requests, to reduce the autoloading process.
        //'module_map_cache_enabled' => $booleanValue,

        // The key used to create the class map cache file name.
        //'module_map_cache_key' => $stringKey,

        // The path in which to cache merged configuration.
        //'cache_dir' => $stringPath,

        // Whether or not to enable modules dependency checking.
        // Enabled by default, prevents usage of modules that depend on other modules
        // that weren't loaded.
        // 'check_dependencies' => true,
    ),

    // Used to create an own service manager. May contain one or more child arrays.
    //'service_listener_options' => array(
    //     array(
    //         'service_manager' => $stringServiceManagerName,
    //         'config_key'      => $stringConfigKey,
    //         'interface'       => $stringOptionalInterface,
    //         'method'          => $stringRequiredMethodName,
    //     ),
    // )

   // Initial configuration with which to seed the ServiceManager.
   // Should be compatible with Zend\ServiceManager\Config.
   // 'service_manager' => array(),
);