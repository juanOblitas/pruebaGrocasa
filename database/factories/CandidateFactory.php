<?php

namespace Database\Factories;

use App\Models\Candidate;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class CandidateFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Candidate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {   
        $users=User::all();
        $num=random_int(1, 10);
        $myUser=null;
        $i=1;
        foreach ($users as $key) {
            if($num==$i){
                $myUser=$key;
                break;
            }
            $i++;
        }
        $owner=$myUser->id;

        return [
            'name' => $this->faker->name,
            'source' => $this->faker->company,
            'owner' => $owner,
        ];
    }
}
