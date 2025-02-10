<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Ad\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request)
    {
        try {
            $name = $request->name;
            $email = $request->email;
            $phone_number = $request->phone_number;
            $blood_type_id = $request->blood_type_id;
            $last_donation_date = $request->last_donation_date;

            $user_repository = new UserRepository();
            $response = $user_repository->update(
                name: $name,
                email: $email,
                phone_number: $phone_number,
                blood_type_id: $blood_type_id,
                last_donation_date: $last_donation_date
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["user"] = $response["user"];

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }
}
