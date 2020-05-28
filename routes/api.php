<?php
//public Routes
Route::get( 'me', 'User\MeController@getMe' );
//Get designs

Route::get( 'designs', 'Designs\DesignController@index' );
Route::get( 'designs/{id}', 'Designs\DesignController@findDesign' );
Route::get( 'designs/slug/{slug}', 'Designs\DesignController@findBySlug' );
//Get Users

Route::get( 'users', 'User\UserController@index' );
Route::get( 'user/{username}', 'User\UserController@findByUsername' );
Route::get( 'users/{id}/designs', 'Designs\DesignController@getForUser' );

Route::get( 'teams/slug/{slug}', 'Teams\TeamsController@findBySlug' );
Route::get( 'teams/{id}/designs', 'Designs\DesignController@getForTeam' );

// Search Designs
Route::get( 'search/designs', 'Designs\DesignController@search' );
Route::get( 'search/designers', 'User\UserController@search' );
// Routes for guests only
Route::group( ['middleware' => ['guest:api']], function () {
    Route::post( 'register', 'Auth\RegisterController@register' );
    Route::get( 'getUsers', 'Auth\RegisterController@getUsers' );
    Route::post( 'verification/verify/{user}', 'Auth\VerificationController@verify' )->name( 'verification.verify' );
    Route::post( 'verification/resend', 'Auth\VerificationController@resend' );
    Route::post( 'login', 'Auth\LoginController@login' );
    Route::post( 'password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail' );
    Route::post( 'password/reset', 'Auth\ResetPasswordController@reset' );

} );

// Route group  for authenticated users only
Route::group( ['middleware' => ['auth:api']], function () {
    Route::post( 'logout', 'Auth\LoginController@logout' );
    Route::post( 'setting/profile', 'User\SettingController@updateProfile' );
    Route::post( 'setting/password', 'User\SettingController@updatePassword' );
    Route::post( 'designs/{id}', 'Designs\DesignController@update' );
    Route::delete( 'designs/{id}', 'Designs\DesignController@destroy' );
    Route::post( 'designs', 'Designs\UploadController@upload' );

    // Likes and Unlikes

    Route::post( 'designs/{id}/like', 'Designs\DesignController@like' );
    Route::get( 'designs/{id}/liked', 'Designs\DesignController@checkIfUserHasLiked' );
    // comments
    Route::post( 'designs/{id}/comments', 'Designs\CommentController@store' );
    Route::put( 'comments/{id}', 'Designs\CommentController@update' );
    Route::delete( 'comments/{id}', 'Designs\CommentController@destroy' );


    Route::group( ['namespace' => 'Teams'], function () {
        //Teams
        Route::post( 'teams', 'TeamsController@store' );
        Route::get( 'teams/{id}', 'TeamsController@findById' );
        Route::get( 'teams', 'TeamsController@index' );
        Route::get( 'users/teams', 'TeamsController@fetchUserTeams' );
        Route::put( 'teams/{id}', 'TeamsController@update' );
        Route::delete( 'teams/{id}', 'TeamsController@destroy' );
        Route::delete( 'teams/{team_id}/users/{user_id}', 'TeamsController@removeFromTeam' );

        //Invitation

        Route::post( 'invitation/{teamId}', 'InvitationController@invite' );
        Route::post( 'invitation/{id}/resend', 'InvitationController@resend' );
        Route::post( 'invitation/{id}/respond', 'InvitationController@respond' );
        Route::delete( 'invitation/{id}', 'InvitationController@destroy' );
    } );
    Route::group( ['namespace' => 'Chat'], function () {
        // Chats
        Route::post( 'chats', 'ChatController@sendMessage' );
        Route::get( 'chats', 'ChatController@getUserChats' );
        Route::get( 'chats/{id}/messages', 'ChatController@getChatMessages' );
        Route::put( 'chats/{id}/markAsRead', 'ChatController@markAsRead' );
        Route::delete( 'messages/{id}', 'ChatController@destroyMessage' );
    } );
} );
