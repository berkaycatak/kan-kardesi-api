<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Ad\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return array|mixed
     */
    public function update(Request $request): mixed
    {
        try {
            $name = $request->name;
            $email = $request->email;
            $phone_number = $request->phone_number;
            $blood_type_id = $request->blood_type_id;
            $city_id = $request->city_id;
            $last_donation_date = $request->last_donation_date;

            $user_repository = new UserRepository();
            $response = $user_repository->update(
                name: $name,
                email: $email,
                phone_number: $phone_number,
                blood_type_id: $blood_type_id,
                city_id: $city_id,
                last_donation_date: $last_donation_date,
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["user"] = $response["user"];

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    public function changePassword(Request $request): mixed
    {
        try {
            $old_password = $request->old_password;
            $new_password = $request->new_password;

            if (empty($old_password) || empty($new_password))
                throw new \Exception("Lütfen tüm alanları doldurun.");

            $user_repository = new UserRepository();
            $response = $user_repository->changePassword(
                old_password: $old_password,
                new_password: $new_password
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["user"] = $response["user"];

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }

    public function getProfile(Request $request): mixed
    {
        try {
            $id = $request->id;

            if (empty($id))
                throw new \Exception("Lütfen tüm alanları doldurun.");

            $user_repository = new UserRepository();
            $response = $user_repository->getProfile(
                id: $id
            );

            if ($response["error"] == 1)
                throw new \Exception($response["msg"]);

            $this->output["status"] = true;
            $this->output["user"] = $response["user"];

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }
}
