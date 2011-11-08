<?

	class Dappy {

		private $error;
		private $errors;

		private $ds;
		private $r;
		private $bindDN;
		private $bindPass;

		private $password;
		private $email;
		private $phone;

		function __construct($uid, $pass) {

			$this->bindPass = $pass;

			$this->ds = @ldap_connect(LDAP_HOST);
			if(!$this->ds) { 
				$this->error = 'Unable to connect to LDAP'; 
			} else {
				$this->bindDN = sprintf("%s=%s,%s", LDAP_UID, $uid, LDAP_BASEDN);
				$this->r = @ldap_bind($this->ds, $this->bindDN, $this->bindPass);
				if(!$this->r)  { $this->error = 'Incorrect username or password'; }	
			}
		}

		public function processRequest( $data ) {

			$this->checkValues($data);

			if(count($this->errors) > 0) {
				$this->error = 'Unable to update record!';
			} else {
				$this->password = $data['try-password'];
				$this->mobile = $data['mobile'];
				$this->email = $data['email'];

				$new["userPassword"] = '{md5}' . base64_encode(pack('H*', md5($this->password)));
				$new["mail"] = $this->email;
				$new["mobile"] = $this->mobile;

				ldap_modify($this->ds, $this->bindDN, $new);
				print 'done';
				die();
			}

		}

		public function checkValues($data) {

			if($data['new-password'] != $data['try-password']) {
				$this->errors['try-password'] = 'Passwords do not match';
			}

			if(strlen($data['new-password']) < 6) {
				$this->errors['new-password'] = 'Password must be mininum 6 characters';
			}

			if(empty($data['new-password'])) {
				$this->errors['new-password'] = 'New password required';
			}

			if(empty($data['try-password'])) {
				$this->errors['try-password'] = 'Password confirmation required';
			}

			if(empty($data['email'])) {
				$this->errors['email'] = 'Email address is required';
			}

			if(empty($data['mobile'])) {
				$this->errors['mobile'] = 'Mobile number is required';
			}

			if(substr($data['mobile'], 0, 4) != '+614') { 
				$this->errors['mobile'] = 'Mobile must start with +614';
			}

			if(strlen($data['mobile']) != 12)  {
				$this->errors['mobile'] = 'Mobile must be 12 characters long';
			}

		}

		public function hasError() { if(empty($this->error)) { return false; } else { return true; } }
		public function getError() { return $this->error; }
		public function getFieldError() { return $this->errors; }

	}

