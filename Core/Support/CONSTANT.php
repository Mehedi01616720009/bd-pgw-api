<?php

namespace Core\Support;

/**
 * Configuration Constants
 * Usage: CONSTANT::DATABASE_HOST or CONSTANT::APP_NAME
 */
class CONSTANT
{
    // Database
    public const DATABASE_HOST = 'database.host';
    public const DATABASE_USER = 'database.user';
    public const DATABASE_PASS = 'database.pass';
    public const DATABASE_NAME = 'database.db_name';
    public const DATABASE_CHARSET = 'database.charset';
    public const DATABASE_COLLATION = 'database.collation';

    // Application
    public const APP_NAME = 'app.name';
    public const APP_URL = 'app.url';
    public const APP_PUBLIC_URL = 'app.public_url';
    public const APP_SUBDIRECTORY = 'app.subdirectory';
    public const APP_TIMEZONE = 'app.timezone';
    public const APP_DEBUG = 'app.debug';
    public const APP_ENVIRONMENT = 'app.environment';
    public const APP_LOGO = 'app.logo';

    // Security
    public const CSRF_ENABLED = 'security.csrf.enabled';
    public const CSRF_TOKEN_NAME = 'security.csrf.token_name';
    public const CSRF_TOKEN_LENGTH = 'security.csrf.token_length';
    public const CSRF_EXPIRE_TIME = 'security.csrf.expire_time';

    public const SESSION_LIFETIME = 'security.session.lifetime';
    public const SESSION_SECURE = 'security.session.secure';
    public const SESSION_HTTP_ONLY = 'security.session.http_only';
    public const SESSION_SAME_SITE = 'security.session.same_site';

    // Bkash
    public const BKASH_SANDBOX = 'payment.bkash.sandbox';
    public const BKASH_VERSION = 'payment.bkash.version';
    public const BKASH_APP_KEY = 'payment.bkash.app_key';
    public const BKASH_APP_SECRET = 'payment.bkash.app_secret';
    public const BKASH_USERNAME = 'payment.bkash.username';
    public const BKASH_PASSWORD = 'payment.bkash.password';
    public const BKASH_BASE_URL = 'payment.bkash.base_url';

    // Nagad
    public const NAGAD_SANDBOX = 'payment.nagad.sandbox';
    public const NAGAD_ACCOUNT = 'payment.nagad.account';
    public const NAGAD_MERCHANTID = 'payment.nagad.merchantid';
    public const NAGAD_MERCHANT_PG_PUBLIC_KEY = 'payment.nagad.merchant_pg_public_key';
    public const NAGAD_MERCHANT_PRIVATE_KEY = 'payment.nagad.merchant_private_key';
    public const NAGAD_BASE_URL = 'payment.nagad.base_url';
}
