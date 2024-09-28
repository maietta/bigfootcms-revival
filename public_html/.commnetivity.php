<?
	define("HOSTNAME", "localhost"); // Required!
	/* If SSL_HOST is set, the system will go into trigger mode for tunneling traffic over VPN. Specify hostname as the value. */
	// define("SSL_HOST", "https://www.example.com"); // To disable support for SSL, add // or # in front.
	define("PATH", $_SERVER["DOCUMENT_ROOT"]);
	define("COMMNETIVITY", "/assets/commnetivity"); // Location of Commnetivity
	define("TEMPLATES", PATH . "/Templates/"); // Add trailing fowardslash.

	define("DB_SERVER", "localhost");
	define("DB_USER", "dbuser");
	define("DB_PASS", "dbpass"); //
	define("DB_NAME", "dbname"); // Change to "name" if user is diffrent that

	define("TABLE_PREFIX", "commnetivity"); // change commnetivity to another table prefix, i.e. table names will become commnetivity_tablename.
	define("DIRECTORY_DEFAULT", "index.html"); // Must not be changed after setting up website, unless you want to make changes in tables.
	define("TEMPLATE_DEFAULT", "default.dwt"); // Do NOT add path. The template must be located in /Templates folder.
	define("TEMPLATE_TRUMPER", "content"); // If template is spefified in both sitelevels and content, which will be used? Choices are "content" or "trumper".

	// Default meta data. If you do not speficy these fields in CMS for each page or sitelevel, these will appear in the template.
	define("DEFAULT_KEYWORDS", "");
	define("DEFAULT_DESCRIPTION", "");
	define("DEFAULT_DISTRIBUTION", "global");

	define("CMS_MIN_LEVEL", 7); // Users must have this number or higher to use CMS functions.
	define("ADMIN", 10); // To become an admin, you must have this number or higher.

	define("RPC_PATH", "/rpc"); // Changes the base location of the RPC script "sitelevel".
	
	define("USER_TIMEOUT", 60*60); // (in minutes)
	define("GUEST_TIMEOUT", 5); // (in minutes)
	define("ADMIN_LOGIN", 15);

	define("COOKIE_EXPIRE", 60*60*24);  // 1 day by default. (Seconds x Minutes x Hours) = 86400 seconds.

	define("EMAIL_FROM_NAME", "");
	define("EMAIL_FROM_ADDR", "");
	define("EMAIL_WELCOME", false);
	define("ALL_LOWERCASE", true);
	
	define("AMMEND_TITLES", " - " . HOSTNAME);

    define("CKEDITOR", "/assets/ckeditor_3.5"); // Editor of choice (USE CKEDITOR)
    define("SIMPLE_DOM_PARSER", "/assets/simplehtmldom_1_11/simple_html_dom.php");
	// Now that we have configured contsants, let's start the engine!
	include_once(PATH.COMMNETIVITY."/index.php");

?>
