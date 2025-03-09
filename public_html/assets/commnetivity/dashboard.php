<?php

if ( $_SESSION['username'] ) { // Logged in?
	if ( VPATH == '/logout' ) {
			$next_url = $_SESSION['referrer'];
			session_destroy();
			header("Location: http://" . HOSTNAME);
			exit;
	} else {
		$profile = $framework->profile($_SESSION['username']);
		$preferences = ( $profile->preferences ) ? (object) unserialize($profile->preferences) : (object) array();
		$welcome_back = 'Welcome back ';
			switch ( $preferences->welcome_back_format ) {
				case "salutation+lname":
					if ( $profile->lname ) {
						$welcome_back .= ( $preferences->honorific ) ? $preferences->honorific . " " . ucfirst($profile->lname) . ". " : $profile->username;
					} else {
						$welcome_back .= $profile->username;
					}
				break;
				case "salutation+ ":
					if ( $profile->fname && $profile->lname ) {
						$welcome_back .= ( $preferences->honorific ) ? $preferences->honorific . " " . ucfirst($profile->fname) . " " . ucfirst($profile->lname) . ". " : $profile->username;
					} else {
						if ( $profile->lname ) { // No first name available, but last name does exist.
							$welcome_back .= ( $preferences->honorific ) ? $preferences->honorific . " " . ucfirst($profile->lname) . ". " : $profile->username;
						} else {
							$welcome_back .= $profile->username;
						}
					}				
				break;
				case "lname": $welcome_back .= ( $profile->lname ) ? ucfirst($profile->name) : $profile->username; break;
				default: $welcome_back .= "<b>" . $profile->username . "</b>. "; break;
			}			
		$welcome_back .= 'Please <a href="/logout">sign out</a> when finished.';
		$dashboard = ( VPATH == '/index.html' )
			? $welcome_back
			: 'You are logged in as ' . $profile->username . '. <a href="/my_account/index.html">Manage Account</a> or <a href="/logout">logout</a>.';
                switch (VPATH) {
					case '/account';    define(TOOLBOX, "cgi"); define(TOOL, "account");    $dynamic = (object) $framework->my_account();	break;
                    case '/inbox';    define(TOOLBOX, "cgi"); define(TOOL, "inbox");    $dynamic = (object) $framework->inbox();	break;
                    default: break;
                }
                $targets = ( $dynamic->targets ) ? (object) $dynamic->targets : (object) array();
       }
} else {
	$profile = array();
	if ( AUTH_TOOLS ) {
		// install switch to force ssl somewhere in here.
		

		switch ( VPATH ) {
			case '/login';      define(TOOLBOX, "cgi"); define(TOOL, "login"); $dynamic = (object) $framework->login(); break;
			case '/logout';     define(TOOLBOX, "cgi"); define(TOOL, "logout");	header("Location: http://" . HOSTNAME);  break;
            case '/register';   define(TOOLBOX, "cgi"); define(TOOL, "register");   $dynamic = $framework->register();	 break;
			case '/activate';   define(TOOLBOX, "cgi"); define(TOOL, "activate");   $dynamic = $framework->activate();       break;
			case '/resetpwd';   define(TOOLBOX, "cgi"); define(TOOL, "resetpwd");   $script_title = "Reset your password";	$script_content = $framework->resetpwd();   break;
			case '/unregister'; define(TOOLBOX, "cgi"); define(TOOL, "unregister"); $script_title = "Cancel your account";	$script_content = $framework->unregister(); break;
			default; break;
		}
	}
	$targets = ( $dynamic->targets ) ? (object) $dynamic->targets : (object) array();
	//$javascripts = ( $dynamic->javascripts ) ? (object) : (object) array();
	if ( $dynamic->javascripts ) {
		//print_r($dynamic->javascripts);
	}
	if ( $dynamic->targets ) {
		//print_r($dynamic->targets);
		//exit;
	} else {
            //print_r($dynamic->targets);
            //exit;
	}
	$dashboard = ( $targets->dashboard ) ? $targets->dashboard : 'You may <a href="http://' . HOSTNAME . '/login">sign-in</a> or <a href="http://' . HOSTNAME . '/register">register</a> for free.';
	//. PROTOCOL
}
