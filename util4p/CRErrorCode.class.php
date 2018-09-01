<?php

class CRErrorCode
{
	/* common */
	const SUCCESS = 0;
	const FAIL = 1;
	const NO_PRIVILEGE = 2;
	const UNKNOWN_ERROR = 3;
	const IN_DEVELOP = 4;
	const INVALID_REQUEST = 5;
	const UNKNOWN_REQUEST = 6;
	const CAN_NOT_BE_EMPTY = 7;
	const INCOMPLETE_CONTENT = 8;
	const FILE_NOT_UPLOADED = 9;
	const RECORD_NOT_EXIST = 10;
	const INVALID_PASSWORD = 11;
	const UNABLE_TO_CONNECT_REDIS = 12;
	const UNABLE_TO_CONNECT_MYSQL = 13;

	/* user */
	const USERNAME_OCCUPIED = 14;
	const EMAIL_OCCUPIED = 15;
	const INVALID_USERNAME = 16;
	const INVALID_EMAIL = 17;
	const WRONG_PASSWORD = 18;
	const NOT_LOGED = 19;
	const USER_NOT_EXIST = 20;
	const USER_IS_BLOCKED = 21;
	const USER_IS_REMOVED = 22;
	const EMAIL_IS_NOT_VERIFIED = 33;

	const USERNAME_MISMATCH_EMAIL = 23;

	const CODE_EXPIRED = 24;
	const EMAIL_ALREADY_VERIFIED = 25;
	const INVALID_COOKIE = 26;

	/* auth */
	const TOKEN_EXPIRED = 27;
	const SITE_NOT_EXIST = 28;
	const INVALID_URL = 29;
	const INVALID_PARAM = 31;
	const DOMAIN_MISMATCH = 32;

	/* rate limit */
	const TOO_FAST = 30;

	public static function getErrorMsg($errno)
	{
		switch ($errno) {
			case CRErrorCode::SUCCESS:
				return 'Success';

			case CRErrorCode::USERNAME_OCCUPIED:
				return 'Username exists !';

			case CRErrorCode::EMAIL_OCCUPIED:
				return 'Email exists !';

			case CRErrorCode::NO_PRIVILEGE:
				return 'You dont\' have permission to do this !';

			case CRErrorCode::INVALID_USERNAME:
				return 'Invalid username !';

			case CRErrorCode::INVALID_EMAIL:
				return 'Invalid email !';

			case CRErrorCode::UNKNOWN_ERROR:
				return 'Unknown error !';

			case CRErrorCode::WRONG_PASSWORD:
				return 'Wrong password !';

			case CRErrorCode::IN_DEVELOP:
				return 'In develop ^_^ !';

			case CRErrorCode::UNABLE_TO_CONNECT_REDIS:
				return 'Unable to connect Redis !';

			case CRErrorCode::UNABLE_TO_CONNECT_MYSQL:
				return 'Unable to connect Mysql !';

			case CRErrorCode::NOT_LOGED:
				return 'You haven\'t loged !';

			case CRErrorCode::USER_NOT_EXIST:
				return 'User not exist !';

			case CRErrorCode::INVALID_REQUEST:
				return 'Invalid request !';

			case CRErrorCode::UNKNOWN_REQUEST:
				return 'Unknown request !';

			case CRErrorCode::CAN_NOT_BE_EMPTY:
				return 'Input is empty !';

			case CRErrorCode::FAIL:
				return 'Failed !';

			case CRErrorCode::INCOMPLETE_CONTENT:
				return 'Cannot be empty !';

			case CRErrorCode::FILE_NOT_UPLOADED:
				return 'Upload failed !';

			case CRErrorCode::RECORD_NOT_EXIST:
				return 'Record not found !';

			case CRErrorCode::USER_IS_BLOCKED:
				return 'Account is blocked !';

			case CRErrorCode::USER_IS_REMOVED:
				return 'Account is removed !';

			case CRErrorCode::INVALID_PASSWORD:
				return 'Invalid password !';

			case CRErrorCode::USERNAME_MISMATCH_EMAIL:
				return 'Username or email not match !';

			case CRErrorCode::CODE_EXPIRED:
				return 'Code is wrong or expires !';

			case CRErrorCode::EMAIL_ALREADY_VERIFIED:
				return 'Email is already verified !';

			case CRErrorCode::TOO_FAST:
				return 'System busy !';

			case CRErrorCode::INVALID_COOKIE:
				return 'Invalid Cookie !';

			case CRErrorCode::TOKEN_EXPIRED:
				return 'Token expired !';

			case CRErrorCode::SITE_NOT_EXIST:
				return 'Site not exist !';

			case CRErrorCode::INVALID_URL:
				return 'Invalid url !';

			case CRErrorCode::INVALID_PARAM:
				return 'Invalid param !';

			case CRErrorCode::DOMAIN_MISMATCH:
				return 'redirect_uri not in allowed hosts !';

			case CRErrorCode::EMAIL_IS_NOT_VERIFIED:
				return 'Verify your email first !';

			default:
				return 'Unknown error(' . $errno . ')';
		}
	}
}
