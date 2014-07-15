<?php
return [
    'asset_bundle' => [
	'production'	    =>	false, // Application environment (Developpement => false)
	'lastModifiedTime'  =>	null, // Arbitrary last modified time in production
	'cachePath'	    =>	'@zfRootPath/public_html/cache', // Cache directory absolute path
	'assetsPath'	    =>	'@zfRootPath/public_html', // Assets directory absolute path 
	'baseUrl'	    =>	null, // Base URL of the application
	'cacheUrl'	    => '@zfBaseUrl/cache/', // Cache directory base url
	'mediaExt'	    => [
	    'jpeg', 
	    'jpg', 
	    'png', 
	    'gif', 
	    'ttf',
	    'eot',
	    'svg',
	    'woff'
	], //Put here all media extensions to be cached
	
	'recursiveSearch'   =>	false, // Allows search for matching assets in required folder and its subfolders
	'filters' => [
	    \AssetsBundle\Service\Service::ASSET_LESS	=>  'LessFilter',
	    \AssetsBundle\Service\Service::ASSET_CSS	=>  'CssFilter',
	    \AssetsBundle\Service\Service::ASSET_JS	=>  'JsFilter',
	    'png'   =>	'PngFilter',
	    'jpg'   =>	'JpegFilter',
	    'jpeg'  =>	'JpegFilter',
	    'gif'   =>	'GifFilter'
	],
	'assets' => [
	    
	    // CSS style for every loaded page
	    
	    'css' => [
		'assets/css/bootstrap.min.css',
		'assets/css/bootstrap-theme.min.css'
	    ],
	    
	    // JavaScript for every loaded page
	    
	    'js' => [
		'assets/js/jquery-1.11.1.min.js',
		'assets/js/bootstrap.min.js',
	    ],
	    'media' => [
		'images/',
		'assets/fonts/glyphicons-halflings-regular.eot',
		'assets/fonts/glyphicons-halflings-regular.woff',
		'assets/fonts/glyphicons-halflings-regular.svg',
		'assets/fonts/glyphicons-halflings-regular.ttf',
	    ]
	]
    ],
];
