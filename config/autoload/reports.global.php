<?php
    
/**
 * Reports file configuration
 *
 */

return [
    'reports' => [
        // The NodeJS bin Path
        'nodeJsBinPath' => '/usr/lib/nodejs',
        
        // The UglifyJS Path (normally installed by an "npm install" at the Reports module root)
        'uglifyJsPath' => realpath(__DIR__.'/../../').'/node_modules/uglify-js/bin/uglifyjs',
    ],
];

