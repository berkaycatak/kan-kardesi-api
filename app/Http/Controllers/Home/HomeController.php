<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Repositories\Blog\BlogRepository;
use App\Http\Repositories\Blood\BloodRepository;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function getHome(Request $request)
    {
        try {
            $bloodRepository = new BloodRepository();
            $blogRepository = new BlogRepository();

            $bloodRequests = $bloodRepository->search(
                city: Auth::user()->id,
                blood_type_id: Auth::user()->blood_type_id,
            );

            $blogs = $blogRepository->getBlogs();

            if ($bloodRequests["error"] == 1)
                throw new \Exception($bloodRequests["msg"]);

            if ($blogs["error"] == 1)
                throw new \Exception($bloodRequests["msg"]);

            $this->output["blood_requests"] = $bloodRequests["blood_requests"];
            $this->output["blogs"] = $blogs["blogs"];
            $this->output["status"] = true;

        }catch (\Exception $exception){
            $this->output['error'] = 1;
            $this->output['status'] = false;
            $this->output['msg'] = $exception->getMessage();
        }

        return $this->output;
    }
}
