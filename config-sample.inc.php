<?php

	/* Mysql */
	/* It is not recommended to use `root` in production environment */
	define('DB_HOST', 'localhost');
	define('DB_PORT', 3306);
	define('DB_NAME', 'quickauth');
	define('DB_USER', 'root');
	define('DB_PASSWORD', '123456');

	/* Redis */
	/* Make sure that your Redis only listens to Intranet */
	define('REDIS_SCHEME', 'tcp');
	define('REDIS_HOST', 'localhost');
	define('REDIS_PORT', 6379);
	define('REDIS_SHOW_ERROR', true);

	/* Site */
	define('BASE_URL', 'http://127.0.0.1'); //make absolute url for SEO and avoid hijack, no '/' at the end
	define('FORCE_VERIFY', false); //if set, unverified user will be refused to login (Warn: leave this switch as it is not finished)
	//define('ALLOW_REGISTRATION', true);
	//define('ALLOW_CUSTOM_UID', true);

	/* Auth */
	define('AUTH_TOKEN_TIMEOUT', 604800); // 3600*24*7 s

	/* Session */
	define('ENABLE_MULTIPLE_LOGIN', true);
	define('BIND_SESSION_WITH_IP', true);
	define('SESSION_TIME_OUT', 1800);// 30 minutes 30*60=1800
	define('ENABLE_COOKIE', true);

	/* Rate Limit */
	define('ENABLE_RATE_LIMIT', true);
	define('RATE_LIMIT_KEY_PREFIX', 'rl');

	/* Email */
	define('ENABLE_EMAIL_ANTISPAM', true);
	//define('MAXIMUM_EMAIL_PER_IP', 8);
	define('MAXIMUM_EMAIL_PER_EMAIL', 5);//last 24 hours
	define('SENDGRID_API_KEY', '');
	define('EMAIL_FROM', 'service@example.com');
