<?php
return array(

	/*
	 * This is a comma separated list of runtimes that you want to initialize the uploader
	 * instance with. It will try to initialize each runtime in order if one fails
	 * it will move on to the next one.
	 * 'gears','html5','flash','browserplus','silverlight','html4'
	 */
	'runtimes' => 'gears,html5,flash,browserplus,silverlight,html4',

	/*
	 * Page URL to where the files will be uploaded to.
	 */
	'url' => \Uri::create('plupload/plupload/upload'),

	/*
	 * Maximum file size that the user can pick. This string can be in the following
	 * formats 100b, 10kb, 10mb.
	 */
	'max_file_size' => '1024mb',

	/*
	 * Enables you to chunk the file into smaller pieces for example if your PHP backend
	 * has a max post size of 1MB you can chunk a 10MB file into 10 requests. To disable
	 * chunking, remove this config option from your setup.
	 */
	'chunk_size' => '128kb',

	/*
	 * Generate unique filenames when uploading. This will generate unqiue filenames for
	 * the files so that they don't for example collide with existing ones on the server.
	 */
	'unique_names' => false,

	/*
	 * Enables plupload to resize the images to clientside to the specified width, height
	 * and quality. Set this to an object with those parameters.
	 */
	'resize' => '',
	
	/*
	 * List of filters to apply when the user selects files. This is currently file
	 * extension filters there are two items for each filter. title and extensions.
	 */
	'filters' => array(
		array('title' => 'Image files', 'extensions' => 'jpg,gif,png'),
		array('title' => 'Zip files', 'extensions' => 'zip'),
	),

	/*
	 * URL to where the SWF file is for the Flash runtime.
	 */
	'flash_swf_url' => Config::get("base_url") . 'assets/plupload/plupload.flash.swf',

	/*
	 * URL to where the XAP file is for the Silverlight runtime.
	 */
	'silverlight_xap_url' => Config::get("base_url") . 'assets/plupload/plupload.silverlight.xap',

	/*
	 * String with the ID of the browse button. Flash, HTML 5 and Silverlight requires
	 * a shim so you need to specify the id of the button that the shim will be placed
	 * above for those runtimes. This option is not required for by the queue widget.
	 */
	'browse_button' => '',

	/*
	 * String with the ID of the element that you want to be able to drop files into
	 * this is only used by some runtimes that support it.
	 */
	'drop_element' => '',

	/*
	 * Element ID to add object elements to, this defaults to the document body element.
	 */
	'container' => '',

	/*
	 * Boolean state if the files should be uploaded using mutlipart instead of direct
	 * binary streams. Doesn't work on WebKit using the HTML 5 runtime.
	 */
	'multipart' => true,

	/*
	 * Object name/value collection with arguments to get posted together with the multipart file.
	 */
	'multipart_params' => array(),

	/*
	 * Comma separated list of features that each runtime must have for it to initialize.
	 */
	'required_features' => '',

	/*
	 * Name/value object with custom headers to add to HTTP requests.
	 */
	'headers' => '',


	/*
	 * Queue widget specific options
	 */

	/*
	 * Function callback that enables you to bind events before the uploader is initialized.
	 */
	'preinit' => '',

	/*
	 * Boolean state if the drag/drop support for all runtimes should be enabled or disabled.
	 * Default is true.
	 */
	'dragdrop' => '',

	/*
	 * Boolean state if it should be possible to rename files before uploading them.
	 * Default is false.
	 */
	'rename' => true,

	/*
	 * Boolean state if you should be able to upload multiple times or not.
	 */
	'multiple_queues' => '',

	/*
	 * Boolean state if Flash should be forced to use URLStream instead of FileReference.upload.
	 */
	'urlstream_upload' => '',

	/*
	 * Locale
	 */
	'locale' => 'ja',

	/*
	 * UI (jquery or jqueryui)
	 */
	'ui' => 'jqueryui',

	/*
	 * Asset Path
	 */
	'asset_path' => 'assets/plupload/',

	/*
	 * Upload Tmporary Path
	 */
	'upload_tmp_dir' => APPPATH . DS . 'tmp' . DS . 'plupload',

	/*
	 * Remove old files
	 */
	'cleanup_upload_tmp' => true,

	/*
	 * Temp file age in seconds
	 */
	'max_tmp_age' => 5 * 3600,

	/*
	 * Execution time
	 */
	'time_limit' => 5 * 60,

);

