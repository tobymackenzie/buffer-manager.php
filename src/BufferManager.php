<?php
namespace TJM\Component\BufferManager;

class BufferManager{
	/*
	Method: end
	Convenience alias method for ending a named or unnamed buffer.
	Parameters:
		named(Boolean|String): whether to end named buffer.  Passing a string does not currently work unless it is the same as the currently opened buffer
	Return: $return(String): Content of buffer.
	*/
	public function end($named = false){
		if($named){
			if($named === true){
				return $this->endNamedBuffer();
			}else{
				return $this->endNamedBuffer($named);
			}
		}else{
			return self::endOutputBuffer();
		}
	}

	/*
	Method: get
	Convenience method alias for getting one or mored named buffers' contents.
	Parameters:
		names(String|Array): name or names to get buffer for.  An array means multiple buffers, a string means one.
	Return (String|Array): if one buffer, will return the string representing that buffer, or null if none exist.  If multiple buffers, will return an array of buffer strings keyed on name.
	*/
	public function get($names){
		if(is_array($names)){
			return $this->getNamedBuffers($names);
		}else{
			return $this->getNamedBuffer($names);
		}
	}

	/*
	Method: has
	Convenience method alias for hasNamedBuffer to see if a named buffer is set
	Parameters:
		name(String): name of buffer
	Return (Boolean): whether or not buffer is set
	*/
	public function has($name){
		return $this->hasNamedBuffer($name);
	}

	/*
	Method: start
	Convenience alias method for starting a named or unnamed buffer
	Parameters:
		opts(Array|String):
			name(String): if specified, will be a named buffer.  Otherwise, will be a plain buffer.  if $opts is a string, will be used as $opts['name']
			callable(callable): {see startOutputBuffer()}
			chunkSize(Integer): {see startOutputBuffer()}
			erase(Boolean): {see startOutputBuffer()}
	*/
	public function start($opts = Array()){
		if(is_string($opts)){
			$opts = Array(
				'name'=> $opts
			);
		}
		if(!isset($opts['callable'])){
			$opts['callable'] = null;
		}
		if(!isset($opts['chunkSize'])){
			$opts['chunkSize'] = 0;
		}
		if(!isset($opts['eraseOrFlags'])){
			$opts['eraseOrFlags'] = null;
		}
		if(isset($opts['name'])){
			return $this->startNamedBuffer($opts['name'], $opts['callable'], $opts['chunkSize'], $opts['erase']);
		}else{
			return self::startOutputBuffer($opts['callable'], $opts['chunkSize'], $opts['erase']);
		}
	}

	//===
	//==management
	//===
	/*
	Method: endOutputBuffer
	Convenience function for ending an output buffer and allowing any functionality to be added as desired.  Returns the output.
	Return: $return(String): Content of buffer.
	*/
	public static function endOutputBuffer(){
		$return = ob_get_contents();
		ob_end_clean();
		return $return;
	}

	/*
	Method: startOutputBuffer
	Convenience function for starting an output buffer and allowing any functionality to be added as desired.
	Parameters:
		callable(callable): {see ob_start()}
		chunkSize(Integer): {see ob_start()}
		eraseOrFlags(Boolean): {see ob_start()}
	*/
	public static function startOutputBuffer($callable = null, $chunkSize = 0, $eraseOrFlags = null){
		if($eraseOrFlags === null){
			ob_start($callable, $chunkSize);
		}else{
			ob_start($callable, $chunkSize, $eraseOrFlags);
		}
		// if($eraseOrFlags === null){
		// 	if($this->isPHP54plus()){
		// 		$eraseOrFlags = PHP_OUTPUT_HANDLER_STD;
		// 	}else{
		// 		$eraseOrFlags = true;
		// 	}
		// }
		// ob_start($callable, $chunkSize, $eraseOrFlags);
		// return $this;
	}

	/*-@ http://us3.php.net/phpversion */
	// public function isPHP54plus(){
	// 	if(!defined('PHP_VERSION_ID')){
	// 		$version = explode('.', PHP_VERSION);

	// 		define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
	// 	}
	// 	if(!defined('PHP_MINOR_VERSION') && PHP_VERSION_ID < 50207){
	// 		define('PHP_MAJOR_VERSION',   $version[0]);
	// 		define('PHP_MINOR_VERSION',   $version[1]);
	// 		define('PHP_RELEASE_VERSION', $version[2]);
	// 	}
	// 	return PHP_MAJOR_VERSION >= 5 && PHP_MINOR_VERSION >= 4;
	// }

	//===
	//==named buffers
	//===
	protected $buffers = Array();
	protected $openBuffers = Array();

	/*
	Method: endConentBuffer
	End last started named content buffer.
	Parameters:
		name(String): name of output buffer to end.  does not currently work.
	Return: $return(String): Content of buffer.
	*/
	public function endNamedBuffer($name = null){
		//--pop name from openBuffers, 'ending' it
		if(!$name){
			$name = array_pop($this->openBuffers);
		}

		if($name){
			//--get content of buffer
			$return = self::endOutputBuffer();

			//--append to existing, if set
			// if($this->hasNamedBuffer($name)){
			// 	$return = $this->buffers[$name] . $return;
			// }

			//--set to stored buffers
			$this->buffers[$name] = $return;

			return $return;
		}else{
			return null;
		}
	}

	/*
	Method: getNamedBuffer
	Get the contents of a named content buffer
	Parameters:
		name(String): name of buffer
	Return:
		(String): content of buffer
	*/
	public function getNamedBuffer($name){
		return ($this->hasNamedBuffer($name))
			? $this->buffers[$name]
			: null
		;
	}

	/*
	Method: getNamedBuffers
	Get the contents of an array of named content buffers
	Parameters:
		names(Array): name of buffer
	Return:
		(String): content of buffer
	*/
	public function getNamedBuffers($names){
		$bufferString = '';
		foreach($names as $name){
			if($this->hasNamedBuffer($name)){
				$bufferString .= $this->getNamedBuffer($name);
			}
		}
		return $bufferString;
	}

	/*
	Method: hasNamedBuffer
	Whether or not a named content buffer is set
	Parameters:
		name(String): name of buffer
	Return:
		(Boolean): whether or not buffer is set
	*/
	public function hasNamedBuffer($name){
		return array_key_exists($name, $this->buffers);
	}

	/*
	Method: startNamedBuffer
	Start a named content buffer, to be stored in an array of named content buffers.
	Parameters:
		callable(callable): {see startOutputBuffer()}
		chunkSize(Integer): {see startOutputBuffer()}
		erase(Boolean): {see startOutputBuffer()}
	*/
	public function startNamedBuffer($name, $callable = null, $chunkSize = 0, $erase = true){
		//--push name onto stack
		$this->openBuffers[] = $name;

		//--start buffer
		self::startOutputBuffer($callable, $chunkSize, $erase);

		return $this;
	}
}
