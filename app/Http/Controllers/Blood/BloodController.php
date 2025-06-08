<?php

namespace App\Http\Controllers\Blood;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Blood\BloodRepository;
use Illuminate\Http\Request;

class BloodController extends Controller
{
    public function sendRequest(Request $request)
    {
        try {
            $bloodRepository = new BloodRepository();

            $required_blood_type_id = $request->required_blood_type_id;
            $units_needed = $request->units_needed;
            $city = $request->city;
            $description = $request->description;

            if ($required_blood_type_id == null || $units_needed == null || $city == null || $description == null)
                throw new \Exception("Lütfen tüm alanları doldurun.");

            $bloodRequest = $bloodRepository->sendRequest(
                $required_blood_type_id,
                $units_needed,
                $city,
                $description
            );

            if ($bloodRequest["error"] == 1)
                throw new \Exception($bloodRequest["msg"]);

            $this->output["blood_request"] = $bloodRequest;
            $this->output["status"] = true;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }

    public function search(Request $request)
    {
        try {
            $bloodRepository = new BloodRepository();

            $city = $request->city;
            $blood_type = $request->blood_type;

            if ($city == null || $blood_type == null)
                throw new \Exception("Lütfen tüm alanları doldurun.");

            $bloodRequests = $bloodRepository->search(
                $city,
                $blood_type
            );

            if ($bloodRequests["error"] == 1)
                throw new \Exception($bloodRequests["msg"]);

            $this->output["blood_requests"] = $bloodRequests;
            $this->output["status"] = true;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }

    public function getBloodTypes()
    {
        try {
            $bloodRepository = new BloodRepository();

            $bloodTypes = $bloodRepository->getBloodTypes();

            if ($bloodTypes["error"] == 1)
                throw new \Exception($bloodTypes["msg"]);

            $this->output["blood_types"] = $bloodTypes["blood_types"];
            $this->output["status"] = true;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }
}
