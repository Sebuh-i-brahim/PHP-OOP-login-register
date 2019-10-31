<?php


class User
{
	private $_db, $_data, $_session,$_cookie, $_islogged;

	function __construct($user = null)
	{
		$this->_db = DB::getInstance();
		$this->_session = Config::get('session/session_name');
		$this->_cookie = Config::get('remember/cookie_name');
		if (!$user) {
			if (Session::exist($this->_session)) {
				$user = Session::get($this->_session);
				if ($this->find($user)) {
					$this->_islogged = true;
				}else{
					$this->_islogged = false;
				}
			}
		}else{
			$this->find($user);
		}
	}

	public function create($data = array())
	{
		if (!$this->_db->insert("users", $data)) {
			throw new Exception("There was an error creating an account");
		}
	}
	public function update($data = array(), $id = null)
	{
		if (!$id && $this->islogged()) {
			$id = $this->data('user_id');
		}
		if (!$this->_db->update('users', $data, $id)) {
			throw new Exception("There was an error Updating Data");	
		}
	}
	public function find($user = null)
	{
		if ($user) {
			if (is_numeric($user)) {
				$column = 'user_id';
			}else{
				$column = (is_email($user))? 'email' : 'username';
			}
			
			$data = $this->_db->select('users', array($column => $user));
			if ($data->_count()) {
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}
	public function login($username = null, $password = null, $remember = false)
	{
		if (!$username && !$password && $this->exist()) {
			Session::put($this->_session, $this->data('user_id'));
		}else{
			$user = $this->find($username);
			if ($user) {
				if ($this->_data->password === Hash::make($password, $this->_data->salt)) {
					Session::put($this->_session, $this->_data->user_id);
					if ($remember) {
						$hash = Hash::unique();
						$hash_db = $this->_db->select('users_session',array('user_id' => $this->data('user_id')));
						if (!$hash_db->_count()) {
							$this->_db->insert('users_session', array(
								'user_id' => $this->data('user_id'),
								'hash' => $hash
							));
						}else{
							$hash = $hash_db->first()->hash;
						}
						
						if(Cookie::put($this->_cookie, $hash, Config::get('remember/cookie_expiry'))){
							
						}else{
							echo "YOXDU";
						}
					}
					return true;
				}
			}
		}	
		return false;
	}
	public function exist()
	{
		return (empty($this->data()))? false: true;
	}
	public function logout()
	{
		$this->_db->delete('users_session', array('user_id' => $this->data('user_id')));
		Session::delete($this->_session);
		Cookie::delete($this->_cookie);
	}
	public function data($column = null)
	{
		if ($column) {
			return $this->_data->$column;
		}
		return $this->_data;
	}
	public function islogged()
	{
		return $this->_islogged;
	}
	public function hasPermission($role)
	{
		$group = $this->_db->select('groups', array('group_id' => $this->data('group_id')));
		if ($group->_count()) {
			$permission = json_decode($group->first('permission'), true);
			if ($permission[$role] == true) {
				return true;
			}
		}
		return false;
	}
}