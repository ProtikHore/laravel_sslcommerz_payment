<?php

namespace App\Repositories;

use Illuminate\Support\ServiceProvider;

class PaymentRepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
        	'App\Repositories\PaymentInterface', 
        	'App\Repositories\PaymentRepository'
        );
    }
}
