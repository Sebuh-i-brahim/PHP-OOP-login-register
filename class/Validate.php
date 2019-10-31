<?php
/**
 * 
 */
class Validate
{
	private $_passed = false,
			$_errors = array(),
			$_conn = null,
			$_input = array(),
			$_data = array(),
			$_validation = array(
				'required' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::require"
				),
				'text' => array(
					"filter" => FILTER_VALIDATE_REGEXP,
					"options" => array("regexp" => "/^[a-zA-Z]+[a-zA-Z ]*$/")
				),
				'email' => FILTER_VALIDATE_EMAIL,
				'min' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::min"
				),
				'max' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::max"
				),
				'unique' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::userFetch"
				),
				'password' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::password"
				),
				'match' => array(
					"filter" => FILTER_CALLBACK,
					"options" => "self::match"
				)
			);
			

	public function __construct()
	{
		$this->_conn = DB::getInstance();
	}
	public function check($filter, $array = array())
	{	
		$c = (empty($array))? $this->input() : $array;
		$input_keys = array_keys($c);
		$filter_keys = array_keys($filter);	
		foreach ($input_keys as $input_key) {	
			foreach ($filter_keys as $filter_key){
				if ($input_key == $filter_key) {
					if (is_object($filter[$filter_key])) {
						foreach ($filter[$filter_key] as $fil_val) {
							foreach ($fil_val as $fil_key => $val) {	
								$a = filter_var_array(array("filt" => $c[$filter_key]),array( "filt" => $val));
								if ($a["filt"] == false) {
									$err = call_user_func_array(array("validation",$fil_key), array($filter_key));
									if(!is_null($err)){ $this->_errors[] = $err;}	
								}else{
									$this->_data[$filter_key] = $a['filt']; 
								}	
							}
						}
					}else{
						foreach ($filter[$filter_key] as $key => $value) {
							$d = filter_var_array(array($filter_key => $c[$filter_key]),array( $filter_key => $filter[$filter_key]));
							if ($d[$filter_key] == false) {
								$err = call_user_func_array(array("validation", $key), array($filter_key));
								if(!is_null($err)){ $this->_errors[] = $err;}	
							}else{
								$this->_data[$filter_key] = $d[$filter_key];
							}
						}	
					}
				}	
			}
		}
	}
	public function removeTag($array)
	{
		$this->_input = filter_var_array($array, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
		return $this;
	}
	public function require($data)
	{
		if (!empty($data)) {
			return $data;
		}
		return false;
	}
	public function set($valid)
	{
		return array($valid => $this->_validation[$valid]);
	}
	public function min($val)
	{
		return (strlen($val) > 2)? $val : false;
	}
	public function max($val)
	{
		return (strlen($val) < 30)? $val : false;
	}
	public function userFetch($data)
	{
		return ($this->_conn->query("SELECT * FROM users WHERE username = '{$data}'")->_count() > 0)? false : $data;
	}
	public function password($psw)
	{
		Session::flash("password", $psw);
		return $psw;
	}
	public function errors()
	{
		return $this->_errors;
	}
	public function input($inp = null)
	{
		return (empty($inp))? $this->_input : $this->_input[$inp];
	}
	public function data($inp = null)
	{
		return (empty($inp))? $this->_data : $this->_data[$inp];
	}
	public function passed()
	{
		return (empty($this->errors()))? true : false;
	}
	public function match($psw)
	{
		if ($psw == Session::flash("password") && !empty($psw)) {
			return $psw;
		}
		return false;
	}
}
?>