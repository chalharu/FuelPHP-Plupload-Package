<?php
namespace PLUPLOAD;
class Plupload
{
	protected static $targetDir = null;
	protected static $cleanupTargetDir = null;
	protected static $maxFileAge = null;
	protected static $_chunk = 0;
	protected static $_chunks = 0;
	protected static $fileName = null;
	protected static $filePath = null;
	protected static $partFileName = null;
	protected static $partFilePath = null;

	protected static $callFunc = null;

	/**
	 * @var array default configuration values
	 */
	protected static $_defaults = array();

	/**
	 * @var array configuration of this instance
	 */
	protected static $config = array();

	public static function _init()
	{
		\Config::load('plupload', true);
		static::$_defaults = array(
			'runtimes' => 'gears,html5,flash,browserplus,silverlight,html4',
			'url' => \Uri::create('plupload/plupload/upload'),
			'max_file_size' => '1024mb',
			'chunk_size' => '128kb',
			'unique_names' => false,
			'resize' => '',
			'filters' => array(
				array('title' => 'Image files', 'extensions' => 'jpg,gif,png'),
				array('title' => 'Zip files', 'extensions' => 'zip'),
			),
			'flash_swf_url' => \Config::get("base_url") . 'assets/plupload/plupload.flash.swf',
			'silverlight_xap_url' => \Config::get("base_url") . 'assets/plupload/plupload.silverlight.xap',
			'browse_button' => '',
			'drop_element' => '',
			'container' => '',
			'multipart' => true,
			'multipart_params' => array(),
			'required_features' => '',
			'headers' => '',
			'preinit' => '',
			'dragdrop' => '',
			'rename' => true,
			'multiple_queues' => '',
			'urlstream_upload' => '',
			'locale' => 'ja',
			'ui' => 'jqueryui',
			'asset_path' => 'assets/plupload/',
			'upload_tmp_dir' => APPPATH . DS . 'tmp' . DS . 'plupload',
			'cleanup_upload_tmp' => true,
			'max_tmp_age' => 5 * 3600,
			'time_limit' => 5 * 60,
		);
		static::$config = array_merge(static::$_defaults, \Config::get('plupload', array()));
	}

	protected static function jsAddSlashes($str)
	{
		$pattern = array(
		"/\\\\/"  , "/\n/"    , "/\r/"    , "/\"/"    ,
		"/\'/"    , "/&/"     , "/</"     , "/>/"
		);
		$replace = array(
		"\\\\\\\\", "\\n"     , "\\r"     , "\\\""    ,
		"\\'"     , "\\x26"   , "\\x3C"   , "\\x3E"
		);
		return preg_replace($pattern, $replace, $str);
	}

	/**
	 * normalize
	 */
	protected static function normalize($list, $sep = ',') {
		if (is_string($list)) {
			$list = explode($sep, $list);
			foreach ($list as $key => $value) {
				$list[$key] = trim($value);
			}
		}
		return $list;
	}

	protected static function getOptions()
	{
		$options = static::$config;
		$filter_func = function ($value) {return ($value === 0 || $value === '0' || !empty($value));};
		$walk_func = function (&$item, $key) {
			if(!in_array( $key,
				array('runtimes','url','max_file_size','chunk_size','unique_names','resize','filters',
					'flash_swf_url','silverlight_xap_url','browse_button','drop_element','container',
					'multipart','multipart_params','required_features','headers','preinit','dragdrop',
					'rename','multiple_queues','urlstream_upload')
			)) {
				$item = NULL;
			}
		};
		array_walk($options, $walk_func);
		$options = array_filter($options, $filter_func);
		return json_encode($options);
	}
	
	public static function insert_jqueryui_header()
	{
		$code ='';

		\Asset::add_path('assets/jquery-ui/js/','js');
		\Asset::add_path('assets/jquery-ui/css/','css');

		$code .= <<< __EOT__
		<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css" type="text/css" />
		<script type="text/javascript">
			$.each(document.styleSheets, function(i,sheet){
				if(sheet.href){
					if(sheet.href.split(':')[1]=='//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css') {
						var tID1 = setInterval(function(){if(sheet.rules || sheet.cssRules){clearInterval(tID1);
						var rules = sheet.rules ? sheet.rules : sheet.cssRules;
						if (rules.length == 0) {
__EOT__;
		$code .= '$(\'' . self::jsAddSlashes(\Asset::css('smoothness/jquery-ui-1.8.21.custom.css')) . '\').appendTo(\'head\');';
		$code .= <<< __EOT__
						}}},10);
					}
				}
			})
		</script>
__EOT__;

		$code .= <<< __EOT__
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/i18n/jquery-ui-i18n.min.js"></script>
__EOT__;
		$code .= "<script>window.jQuery.ui || document.write('" . self::jsAddSlashes(\Asset::js(array('jquery-ui-1.8.21.custom.min.js'))) . "')</script>" . PHP_EOL;

		return $code;
	}

	public static function insert_jquery_header()
	{
		$code ='';

		$code .= <<< __EOT__
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"><\/script>')</script>
		<script>window.jQuery || document.write('<script src="//code.jquery.com/jquery-1.7.2.min.js"><\/script>')</script>
__EOT__;
		$code .= "<script>window.jQuery || document.write('" . self::jsAddSlashes(\Asset::js(array('jquery-1.7.2.min.js'))) . "')</script>" . PHP_EOL;

		return $code;
	}

	public static function insert_header()
	{
		$code ='';

		\Asset::add_path(static::$config['asset_path'],'js');
		\Asset::add_path(static::$config['asset_path'],'css');
		switch(static::$config['ui']){
			case 'jquery':
				$code .= \Asset::css('jquery.plupload.queue/css/jquery.plupload.queue.css');
				break;
			case 'jqueryui':
			default:
				$code .= \Asset::css('jquery.ui.plupload/css/jquery.ui.plupload.css');
				break;
		}
		$code .= \Asset::js('plupload.js');
		$assets = self::normalize(static::$config['runtimes']);
		if(count($assets)>2){
			$code .= \Asset::js('plupload.full.js');
		}else{
			foreach($assets as $asset){
				$code .= \Asset::js( 'plupload.' . $asset . '.js' );
			}
		}
		switch(static::$config['ui']){
			case 'jquery':
				$code .= \Asset::js('jquery.plupload.queue/jquery.plupload.queue.js');
				break;
			case 'jqueryui':
			default:
				$code .= \Asset::js('jquery.ui.plupload/jquery.ui.plupload.js');
				break;
		}
		$locale = static::$config['locale'];
		if(!empty($locale)){
			$code .= \Asset::js('i18n/' . static::$config['locale'] . '.js');
		}
		$code .= <<< __EOT__
	<script type="text/javascript">
	// Convert divs to queue widgets when the DOM is ready
	$(function() {
__EOT__;
		$code .= '$("#uploader").' . ((static::$config['ui'] == 'jquery') ? 'pluploadQueue' : 'plupload') . '(';
		$code .= self::getOptions();
		$code .= <<< __EOT__
		);
	});
	</script>
__EOT__;
		return $code;
	}

	public static function upload($callback = null)
	{
		if(is_callable($callback)){
			self::setUploadFinishCallback($callback);
		}
		self::$targetDir = static::$config['upload_tmp_dir'];
		self::$cleanupTargetDir = static::$config['cleanup_upload_tmp'];
		self::$maxFileAge = static::$config['max_tmp_age'];
		self::setTimeLimit(static::$config['time_limit']);
		self::$_chunk = intval(\Input::post('chunk')) ? : 0;
		self::$_chunks = intval(\Input::post('chunks')) ? : 0;

		self::setFile(self::$targetDir);

		if(self::$cleanupTargetDir)
			self::_removeOldFile();

		if (self::isMultipart()) {
			self::_multipartUpload();
		} else {
			self::_streamUpload();
		}
		if (self::isLastChunk()) {
			rename( self::$partFilePath, self::$filePath);
			if(is_callable(self::$callFunc)){
				call_user_func(self::$callFunc);
			}
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
			//アップロード終了時処理
		} else {
			die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
		}
	}

	/**
	 * _multipartUpload
	 * Multipart upload
	 */
	protected static function _multipartUpload() {
		if(is_uploaded_file(\Input::file('file.tmp_name'))) {
			$out = fopen( self::$partFilePath, self::$_chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen(\Input::file('file.tmp_name'), "rb");
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else {
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
				}
				fclose($in);
				fclose($out);
				@unlink(\Input::file('file.tmp_name'));
			} else {
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			}
		} else {
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}
	}

	/**
	 * _streamUpload
	 * Stream upload
	 */
	protected static function _streamUpload() {
		// Open temp file
		$out = fopen( self::$partFilePath, self::$_chunk == 0 ? "wb" : "ab");
		if ($out) {
			// Read binary input stream and append it to temp file
			$in = fopen("php://input", "rb");

			if ($in) {
				while ($buff = fread($in, 4096))
					fwrite($out, $buff);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

			fclose($in);
			fclose($out);
		} else {
			die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}
	}

	/**
	 * Check last chunk
	 * @return boolean
	 */
	protected static function isLastChunk(){
		if(self::$_chunk == (self::$_chunks - 1)){
			return true;
		}
		return false;
	}
	
	/**
	 * isMultipart
	 * @return boolean 
	 */
	protected static function isMultipart(){
		// Look for the content type header
		return(strpos(\Input::server('CONTENT_TYPE',\Input::server('HTTP_CONTENT_TYPE',NULL)), "multipart") !== false);
	}

	/**
	 * setTimeLimit
	 * @param int $timeLimit 
	 */
	protected static function setTimeLimit($time_limit){
		set_time_limit($time_limit);
	}

	/**
	 * setFile
	 * Set upload directry & filename
	 */
	protected static function setFile(){
		$fileName = \Input::post('name')? : '';
		self::$fileName = preg_replace('/[^\w\._]+/', '_', $fileName);
		if (!file_exists(self::$targetDir))
			mkdir(self::$targetDir);
		self::_createUniqueFile();
		self::$filePath = self::$targetDir . DS . self::$fileName;
		self::$partFilePath = self::$targetDir . DS . self::$partFileName;
	}

	/**
	 * Generate UUID Version 4
	 *
	 * @access public
	 * @static
	 */
	public static function uuidV4() {
		return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',

			// 32 bits for "time_low"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff),

			// 16 bits for "time_mid"
			mt_rand(0, 0xffff),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand(0, 0x0fff) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand(0, 0x3fff) | 0x8000,

			// 48 bits for "node"
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	/**
	 * createUniqueFile
	 * Create unique filename
	 */
	protected static function _createUniqueFile(){
		$md5fn = md5(self::$fileName);
		if(self::$_chunk == 0) {
			do{
				self::$partFileName = self::uuidV4() . $md5fn . ".part";
			} while (file_exists(self::$targetDir . DS . self::$partFileName));
			\Session::set('chunkFileName-' . $md5fn, self::$partFileName);
		} else {
			self::$partFileName = \Session::get('chunkFileName-' . $md5fn);
		}
		
		if(self::isLastChunk()){
			\Session::delete('chunkFileName-' . $md5fn);
			if(file_exists(self::$targetDir . DS . self::$fileName)) {
				$ext = strrpos(self::$fileName, '.');
				$fileName_a = substr(self::$fileName, 0, $ext);
				$fileName_b = substr(self::$fileName, $ext);
	
				$count = 1;
				while (file_exists(self::$targetDir . DS . $fileName_a . '_' . $count . $fileName_b))
					$count++;
	
				self::$fileName = $fileName_a . '_' . $count . $fileName_b;
			}
		}
	}

	/**
	 * removeOldFile
	 * Remove old temp files
	 */
	protected static function _removeOldFile(){
		if (is_dir(self::$targetDir) && ($dir = opendir(self::$targetDir))) {
			while (($file = readdir($dir)) !== false) {
				$tmpfilePath = self::$targetDir . DS . $file;

				// Remove temp file if it is older than the max age and is not the current file
				if (preg_match('/\$/', $file) && (filemtime($tmpfilePath) < time() - self::$maxFileAge) && ($tmpfilePath != self::$partFilePath)) {
					@unlink($tmpfilePath);
				}
			}

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
	}

	public static function setUploadFinishCallback($callFunc = null){
		self::$callFunc = $callFunc;
	}
}
