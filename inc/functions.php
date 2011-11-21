<?

	class Dappy {

		private $error;
		private $errors;

		private $ds;
		private $r;
		private $bindDN;
		private $bindPass;

		private $password;
		private $mail;
		private $phone;

		private $data;

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

		public function getData( $attribute ) {

			if(!is_array($this->data)) {
				$dn = ldap_read($this->ds, $this->bindDN, '(objectclass=*)', array("mail", "mobile", "manager", "objectclass", "sambaNTPassword", "sambaDomainName", "sambaPwdLastSet")); 
				$data = ldap_get_entries($this->ds, $dn);
				$this->data = array();
				$this->data['mobile'] = $data[0]['mobile'][0];
				$this->data['mail'] = $data[0]['mail'][0];
				$this->data['objectclass'] = $data[0]['objectclass'];
				$this->data['domain'] = $data[0]['sambadomainname'][0];
				$this->data['sambantpassword'] = $data[0]['sambantpassword'][0];
				$manager = $data[0]['manager'][0];
				if(!empty($manager)) {
					$this->data['manager'] = preg_replace('/^uid=(.*),ou=(.*).*/', '\\1', $manager);
				}
			}

			return $this->data[$attribute];
		}

		public function processRequest( $data ) {

			$this->checkValues($data);

			if(count($this->errors) > 0) {
				$this->error = 'Unable to update record!';
			} else {
				$this->password = $data['try-password'];
				$this->mobile = $data['mobile'];
				$this->mail = $data['mail'];

				$new["userPassword"] = '{md5}' . base64_encode(pack('H*', md5($this->password)));
		
				$class = $this->getData('objectclass');

				if(in_array('sambaSamAccount', $class)) {
					$new["sambaNTPassword"] = strtoupper(hash('md4', iconv("UTF-8","UTF-16LE",$this->password)));
					$new["sambaPwdLastSet"] = time();
				}

				$new["mail"] = $this->mail;
				$new["mobile"] = $this->mobile;

				if(ldap_modify($this->ds, $this->bindDN, $new)) {
					return true;
				} else {
					$this->error = 'An error occured updating your profile';
				};
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

			if(empty($data['mail'])) {
				$this->errors['mail'] = 'Email address is required';
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

