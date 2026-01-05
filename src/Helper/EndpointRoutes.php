<?php

namespace App\Helper;

class EndpointRoutes
{
    # -- User -- #
    public const USER_REGISTER_POST = 'api_user_register_post';
    public const USER_LOGIN_POST = 'api_user_login_post';
    public const USER_LOGOUT_POST = 'api_user_logout_post';

    # -- Token -- #
    public const TOKEN_REFRESH_POST = 'api_token_refresh_post';
}