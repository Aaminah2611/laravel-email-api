<?php

return [
    'realm_public_key' => env('KEYCLOAK_REALM_PUBLIC_KEY', null),
    
    'algorithms' => env('KEYCLOAK_ALGORITHMS', 'RS256'),
    
    'token_encryption_algorithm' => env('KEYCLOAK_TOKEN_ENCRYPTION_ALGORITHM', 'RS256'),
    
    'load_user_from_database' => env('KEYCLOAK_LOAD_USER_FROM_DATABASE', true),
    
    'user_provider_credential' => env('KEYCLOAK_USER_PROVIDER_CREDENTIAL', 'email'),
    
    'token_principal_attribute' => env('KEYCLOAK_TOKEN_PRINCIPAL_ATTRIBUTE', 'preferred_username'),
    
    'append_decoded_token' => env('KEYCLOAK_APPEND_DECODED_TOKEN', false),
    
    'allowed_resources' => env('KEYCLOAK_ALLOWED_RESOURCES', 'email-api'),
    
    'ignore_resources_validation' => env('KEYCLOAK_IGNORE_RESOURCES_VALIDATION', false),
    
    'leeway' => env('KEYCLOAK_LEEWAY', 0),
    
    'input_key' => env('KEYCLOAK_TOKEN_INPUT_KEY', null),
    
    'user_provider_custom_retrieve_method' => 'findByKeycloakIdOrEmail',
    
    'login_url' => env('KEYCLOAK_LOGIN_URL', 'http://localhost:8080/auth/realms/yourrealm/protocol/openid-connect/auth'),
    
    // Auth server configuration
    'auth_server_url' => env('KEYCLOAK_AUTH_SERVER_URL', 'http://localhost:8080'),
    
    'realm' => env('KEYCLOAK_REALM', 'email-api-realm'),
    
    'client_id' => env('KEYCLOAK_CLIENT_ID', 'email-api'),
    
    'client_secret' => env('KEYCLOAK_CLIENT_SECRET', null),
    
    'redirect' => env('KEYCLOAK_REDIRECT', 'http://localhost/callback'),
    
    'api_audience' => env('KEYCLOAK_API_AUDIENCE', 'email-api'),
];