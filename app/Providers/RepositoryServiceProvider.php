<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contacts\{ChatInterface,
    CommentInterface,
    DesignInterface,
    InvitationInterface,
    MessageInterface,
    TeamInterface,
    UserInterface};
use App\Repositories\Eloquent\{ChatRepositories,
    CommentRepositories,
    DesignRepositories,
    InvitationRepositories,
    MessageRepositories,
    TeamRepositories,
    UserRepositories};

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(DesignInterface::class,DesignRepositories::class);
        $this->app->bind(UserInterface::class,UserRepositories::class);
        $this->app->bind(CommentInterface::class,CommentRepositories::class);
        $this->app->bind(TeamInterface::class,TeamRepositories::class);
        $this->app->bind(InvitationInterface::class,InvitationRepositories::class);
        $this->app->bind(MessageInterface::class,MessageRepositories::class);
        $this->app->bind(ChatInterface::class,ChatRepositories::class);
    }
}
