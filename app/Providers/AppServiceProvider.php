<?php

namespace App\Providers;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Builder::macro('firstOrMake', function ($attributes) {
            $model = static::where($attributes);

            return $model->exists() ? $model->first() : static::make($attributes);
        });

        Command::macro('askWithValidation', function ($question, $field, $rules) {
            $value = $this->ask($question);

            if($message = validateInput($rules, $field, $value)) {
                $this->error($message);

                return $this->askWithValidation($question, $field, $rules);
            }

            return $value;
        });
    }
}
