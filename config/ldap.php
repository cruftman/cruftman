<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Connections (Connection presets)
    |--------------------------------------------------------------------------
    |
    | Configuration parameters for LDAP connections. Define as many connections
    | as you need. These named connection presets may be later referenced from
    | within other parts of this configuration.
    |
    | Detailed documentation of supported connection parameters may be found
    | in the documentation of the korowai framework (see the following link).
    |
    |   https://korowai-framework.readthedocs.io/en/latest/lib/ldap/config.html
    |
    | In most cases it's enough to just provide the 'uri' parameter.
    |
    */
    'connections' => [
        'default' => [
            'uri' => env('LDAP_DEFAULT_URI', 'ldap://cruftman.local')
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Bindings (Binding presets)
    |--------------------------------------------------------------------------
    |
    | Bind parameters for predefined LDAP accounts. Define as many bindings as
    | you need. These named presets may be later referenced from other parts of
    | this configuration. Template bindings with ${placeholders} are supported
    | as well.
    |
    | Each binding is a two-element array, with bind DN at offset 0 and
    | password at offset 1.
    |
    | Example:
    |
    |   'bindings' => [
    |       'admin' => ['cn=admin,dc=example,dc=org', 'secret'],
    |        ...
    |   ],
    |
    */
    'bindings' => [
        'london-user-authenticator'     => [env('LDAP_1_BIND_DN'), env('LDAP_1_PASSWORD')],
        'manchester-user-authenticator' => [env('LDAP_2_BIND_DN'), env('LDAP_2_PASSWORD')],
        'london-user-finder'            => [env('LDAP_3_BIND_DN'), env('LDAP_3_PASSWORD')],
        'manchester-user-finder'        => [env('LDAP_4_BIND_DN'), env('LDAP_4_PASSWORD')],
//
//        // Template bindings
//        'global-person'     => ['uid=${username},ou=people,dc=example,dc=org', '${password}'],
//        'london-person'     => ['uid=${username},ou=people,ou=london,dc=example,dc=org', '${password}'],
//        'manchester-person' => ['uid=${username},ou=people,ou=manchester,dc=example,dc=org', '${password}'],
//        'entry'             => ['${dn}', '${password}']
    ],

    /*
    |--------------------------------------------------------------------------
    | Sessions (Session presets)
    |--------------------------------------------------------------------------
    |
    | An array of predefined LDAP sessios. Define as many sessions as you need.
    | An LDAP session is an object that allows interacting with LDAP server via
    | single LDAP connection. The session is configured with connection
    | settings and binding parameters.
    |
    | Configuration options for a single session:
    |
    |   - connection
    |       name of a connection used by the session, must be one of the keys form
    |       'connections' array (above),
    |   - bind
    |       name of LDAP account binding used by the session, must be one of
    |       the keys from 'bindings' array (above),
    */
    'sessions' => [
        'london-user-authenticator@default' => [
            'connection' => 'default',
            'bind' => 'london-user-authenticator',
        ],
        'manchester-user-authenticator@default' => [
            'connection' => 'default',
            'bind' => 'manchester-user-authenticator',
        ],
        'london-user-finder@default' => [
            'connection' => 'default',
            'bind' => 'london-user-finder',
        ],
        'manchester-user-finder@default' => [
            'connection' => 'default',
            'bind' => 'manchester-user-finder',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Searches (Search presets)
    |--------------------------------------------------------------------------
    |
    | An array of predefined LDAP search queries. Define as may searches as you
    | need. These named searches may be later references from within other parts
    | of the config.
    |
    | Each search query configuration accepts following parameters:
    |
    |   - base
    |       base DN used as search start point,
    |   - filter
    |       search filter,
    |   - options
    |       other options such as search scope, attributes, etc.
    |
    | If a search query is parametrized within an application, the config may
    | introduce search parameters using ${placeholders}. For example, if there
    | is a search query which has parameter named 'username', the config can
    | use the ${username} placeholder in any of its options, for example in
    | filter:
    |
    |   'filter' => '(uid=${username})'
    |
    | The placeholder will be substituted with corresponding parameter's value.
    */
    'searches' => [
        //
        // List all users.
        //
        'global-users' => [
            'base' => 'ou=people,dc=example,dc=org',
            'filter' => '(&(accountstatus=enabled)(enabledservice=cruftman))',
            'options' => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'london-users' => [
            'base' => 'ou=people,ou=london,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'manchester-users' => [
            'base'      => 'ou=people,ou=manchester,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        //
        // Search user by username.
        //
        'global-user-by-name' => [
            'base'      => 'ou=people,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(uid=${username}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'london-user-by-name' => [
            'base'      => 'ou=people,ou=london,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(uid=${username}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'manchester-user-by-name' => [
            'base'      => 'ou=people,ou=manchester,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(uid=${username}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        //
        // Locate user by unique identifier (like an uuid).
        //
        'global-user-by-uuid' => [
            'base'      => 'ou=people,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(entryuuid=${useruuid}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'london-user-by-uuid' => [
            'base'      => 'ou=people,ou=london,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(entryuuid=${useruuid}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
        'manchester-user-by-uuid' => [
            'base'      => 'ou=people,ou=manchester,dc=example,dc=org',
            'filter'    => '(&(accountstatus=enabled)(enabledservice=cruftman)(entryuuid=${useruuid}))',
            'options'   => ['scope' => 'one', 'attributes' => ['*', 'entryuuid']],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication sources (AuthSource presets)
    |--------------------------------------------------------------------------
    |
    | An array of predefined authentication sources. Define as many sources as
    | you need for your authentication schema. An authentication source provides
    | a way to search for a user using its username, to locate user using some
    | sort of unique identifier like OpenLDAP entryUUID, and to authenticate user
    | against LDAP once it is uniquely determined. The configuration of a single
    | authentication source consists of several pieces:
    |
    |   - sessions
    |       an array of Session presets used for searching; this option becomes
    |       required when 'search' or 'locate' options are present,
    |   - search
    |       a Search preset used to search for a user in LDAP when its unique
    |       identifier (like entryUUID) is NOT known to application,
    |   - locate
    |       a Search preset used to search for a user in LDAP when its unique
    |       identifier (like entryUUID) is known to application,
    |   - attempt
    |       settings used to authenticate user, this is an array with the
    |       following nested options
    |
    |       - bind
    |           a Binding preset providing bind DN and password for the
    |           bind method,
    |       - connections
    |           an array of Connection presets to use for binding,
    |       - filter
    |           an LDAP search filter to re-check user's entry after it's
    |           successfully authenticated; the filter provides a way to
    |           filter-out disabled accounts when using "direct bind"
    |           authentication method (i.e. authenticating without prior
    |           search).
    |
    | The 'sessions' and 'attempt.connections' arrays are used to setup a
    | failover strategy. The consecutive Session/Connection presets from these
    | arrays are tried in sequence in case of connection errors.
    |
    | The 'sessions' array is used when searching for a user prior to
    | attempting its authentication (we call this as an "indirect bind"). If none
    | of 'search' nor 'locate' is present, then it's assumed that a "direct bind"
    | authentication is requested, in which case the application invokes bind
    | function on an already known DN, without searching for user's entry.
    |
    */
    'auth_sources' => [
        'global-users' => [
            'attempt' => [
                'connections' => ['default'],
                'bind' => ['uid=${username},ou=people,dc=example,dc=org', '${password}'],
                'filter' => '(&(accountstatus=enabled)(enabledservice=cruftman))',
                'attributes' => ['*', 'entryuuid'],
            ]
        ],
        'london-users' => [
            'sessions' => ['london-user-authenticator@default'],
            'search' => 'london-user-by-name',
            'locate' => 'london-user-by-uuid',
            'attempt' => [
                'bind' => ['${dn}', '${password}'],
            ]
        ],
        'manchester-users' => [
            'sessions' => ['manchester-user-authenticator@default'],
            'search' => 'manchester-user-by-name',
            'locate' => 'manchester-user-by-uuid',
            'attempt' => [
                'bind' => ['${dn}', '${password}'],
            ]
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication schema
    |--------------------------------------------------------------------------
    |
    */
    'auth_schema' => [
        'sources' => [ 'global-users', 'london-users', 'manchester-users' ],
        'ambiguous' => 'first',
    ]


//
//    /*
//    |--------------------------------------------------------------------------
//    | Logging
//    |--------------------------------------------------------------------------
//    |
//    | This option enables logging all LDAP operations on all configured
//    | connections such as bind requests and CRUD operations.
//    |
//    | Log entries will be created in your default logging stack.
//    |
//    | This option is extremely helpful for debugging connectivity issues.
//    |
//    */
//
//    'logging' => env('LDAP_LOGGING', false),
//
//    /*
//    |--------------------------------------------------------------------------
//    | Connections
//    |--------------------------------------------------------------------------
//    |
//    | This array stores the connections that are added to Adldap. You can add
//    | as many connections as you like.
//    |
//    | The key is the name of the connection you wish to use and the value is
//    | an array of configuration settings.
//    |
//    */
//
//    'connections' => [
//
//        'default' => [
//
//            /*
//            |--------------------------------------------------------------------------
//            | Auto Connect
//            |--------------------------------------------------------------------------
//            |
//            | If auto connect is true, Adldap will try to automatically connect to
//            | your LDAP server in your configuration. This allows you to assume
//            | connectivity rather than having to connect manually
//            | in your application.
//            |
//            | If this is set to false, you **must** connect manually before running
//            | LDAP operations. Otherwise, you will receive exceptions.
//            |
//            */
//
//            'auto_connect' => env('LDAP_AUTO_CONNECT', true),
//
//            /*
//            |--------------------------------------------------------------------------
//            | Connection
//            |--------------------------------------------------------------------------
//            |
//            | The connection class to use to run raw LDAP operations on.
//            |
//            | Custom connection classes must implement:
//            |
//            |  Adldap\Connections\ConnectionInterface
//            |
//            */
//
//            'connection' => Adldap\Connections\Ldap::class,
//
//            /*
//            |--------------------------------------------------------------------------
//            | Connection Settings
//            |--------------------------------------------------------------------------
//            |
//            | This connection settings array is directly passed into the Adldap constructor.
//            |
//            | Feel free to add or remove settings you don't need.
//            |
//            */
//
//            'settings' => [
//
//                /*
//                |--------------------------------------------------------------------------
//                | Schema
//                |--------------------------------------------------------------------------
//                |
//                | The schema class to use for retrieving attributes and generating models.
//                |
//                | You can also set this option to `null` to use the default schema class.
//                |
//                | For OpenLDAP, you must use the schema:
//                |
//                |   Adldap\Schemas\OpenLDAP::class
//                |
//                | For FreeIPA, you must use the schema:
//                |
//                |   Adldap\Schemas\FreeIPA::class
//                |
//                | Custom schema classes must implement Adldap\Schemas\SchemaInterface
//                |
//                */
//
//                'schema' => Adldap\Schemas\OpenLDAP::class,
//
//                /*
//                |--------------------------------------------------------------------------
//                | Account Prefix
//                |--------------------------------------------------------------------------
//                |
//                | The account prefix option is the prefix of your user accounts in LDAP directory.
//                |
//                | This string is prepended to all authenticating users usernames.
//                |
//                */
//
//                'account_prefix' => env('LDAP_ACCOUNT_PREFIX', ''),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Account Suffix
//                |--------------------------------------------------------------------------
//                |
//                | The account suffix option is the suffix of your user accounts in your LDAP directory.
//                |
//                | This string is appended to all authenticating users usernames.
//                |
//                */
//
//                'account_suffix' => env('LDAP_ACCOUNT_SUFFIX', ''),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Domain Controllers
//                |--------------------------------------------------------------------------
//                |
//                | The domain controllers option is an array of servers located on your
//                | network that serve Active Directory. You can insert as many servers or
//                | as little as you'd like depending on your forest (with the
//                | minimum of one of course).
//                |
//                | These can be IP addresses of your server(s), or the host name.
//                |
//                */
//
//                'hosts' => explode(' ', env('LDAP_HOSTS', 'corp-dc1.corp.acme.org corp-dc2.corp.acme.org')),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Port
//                |--------------------------------------------------------------------------
//                |
//                | The port option is used for authenticating and binding to your LDAP server.
//                |
//                */
//
//                'port' => env('LDAP_PORT', 389),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Timeout
//                |--------------------------------------------------------------------------
//                |
//                | The timeout option allows you to configure the amount of time in
//                | seconds that your application waits until a response
//                | is received from your LDAP server.
//                |
//                */
//
//                'timeout' => env('LDAP_TIMEOUT', 5),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Base Distinguished Name
//                |--------------------------------------------------------------------------
//                |
//                | The base distinguished name is the base distinguished name you'd
//                | like to perform query operations on. An example base DN would be:
//                |
//                |        dc=corp,dc=acme,dc=org
//                |
//                | A correct base DN is required for any query results to be returned.
//                |
//                */
//
//                'base_dn' => env('LDAP_BASE_DN', 'dc=corp,dc=acme,dc=org'),
//
//                /*
//                |--------------------------------------------------------------------------
//                | LDAP Username & Password
//                |--------------------------------------------------------------------------
//                |
//                | When connecting to your LDAP server, a username and password is required
//                | to be able to query and run operations on your server(s). You can
//                | use any user account that has these permissions. This account
//                | does not need to be a domain administrator unless you
//                | require changing and resetting user passwords.
//                |
//                */
//
//                'username' => env('LDAP_USERNAME'),
//                'password' => env('LDAP_PASSWORD'),
//
//                /*
//                |--------------------------------------------------------------------------
//                | Follow Referrals
//                |--------------------------------------------------------------------------
//                |
//                | The follow referrals option is a boolean to tell active directory
//                | to follow a referral to another server on your network if the
//                | server queried knows the information your asking for exists,
//                | but does not yet contain a copy of it locally.
//                |
//                | This option is defaulted to false.
//                |
//                */
//
//                'follow_referrals' => false,
//
//                /*
//                |--------------------------------------------------------------------------
//                | SSL & TLS
//                |--------------------------------------------------------------------------
//                |
//                | If you need to be able to change user passwords on your server, then an
//                | SSL or TLS connection is required. All other operations are allowed
//                | on unsecured protocols.
//                |
//                | One of these options are definitely recommended if you
//                | have the ability to connect to your server securely.
//                |
//                */
//
//                'use_ssl' => env('LDAP_USE_SSL', false),
//                'use_tls' => env('LDAP_USE_TLS', false),
//
//            ],
//
//        ],
//
//    ],
//
];
