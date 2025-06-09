<?php

namespace App\Http\Repositories\Blog;

use App\Http\Repositories\Repository;
use App\Models\Blog;
use App\Models\BloodCompatibility;
use App\Models\BloodRequest;
use App\Models\BloodType;

class BlogRepository extends Repository
{
    /**
     * @return mixed
     */
    public function getBlogs(): mixed
    {
        try {
            $this->output["blogs"] = Blog::with(["user"])->get();
            return $this->output;

        } catch (\Exception $exception) {
            $this->output["error"] = 1;
            $this->output["msg"] = $exception->getMessage();
            return $this->output;
        }
    }

}
