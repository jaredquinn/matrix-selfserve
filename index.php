<?
	define('LDAP_HOST', '127.0.0.1');
	define('LDAP_UID', 'uid');
	define('LDAP_BASEDN', 'ou=People,dc=cats,dc=lair');

	session_start();

	include "inc/functions.php";
	include "page/header.php";

	$action = strtolower($_REQUEST['action']);

	switch($action) {

		case '':
				include "view/login.php";
				break;
				
		case 'login':

			$l = new Dappy( $_REQUEST['username'], $_REQUEST['password'] );

			if($l->hasError()) {
				$error = $l->getError();
				include "view/login.php";
			} else {
				$_SESSION['username'] = $_REQUEST['username'];
				$_SESSION['password'] = $_REQUEST['password'];
				$_REQUEST['mail'] = $l->getData('mail');
				$_REQUEST['mobile'] = $l->getData('mobile');
				include "view/update.php";
			}
			break;

		case 'update':

			$l = new Dappy( $_SESSION['username'], $_SESSION['password'] );
			$l->processRequest( $_REQUEST );

			if($l->hasError()) {
				$error = $l->getError();
				$err = $l->getFieldError();
				include "view/update.php";
			} else {
				include "view/done.php";
			} 
			break;

		default:
			include "view/error.php";
			break;
	}

	include "page/footer.php";

?>

