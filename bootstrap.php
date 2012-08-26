<?php
Autoloader::add_core_namespace('Plupload', __DIR__ . '/classes/');
Autoloader::add_classes(array(
	'Plupload\\Plupload' => __DIR__ . '/classes/plupload.php',
    'Plupload\\Profiler' => __DIR__ . '/classes/profiler.php',
));
