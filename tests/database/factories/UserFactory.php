<?php

namespace TCG\Voyager\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
 
class UserFactory extends Factory
{
    protected $model = \TCG\Voyager\Models\User::class;

    public function definition()
    {
        static $password;

        return [
            'name'           => $this->faker->name(),
            'role_id'        => 1,
            'email'          => $this->faker->unique()->safeEmail(),
            'password'       => $password ?: $password = bcrypt('secret'),
            'remember_token' => Str::random(10),
        ];
    }
}
