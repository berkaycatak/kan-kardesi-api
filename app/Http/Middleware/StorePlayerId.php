<?php

namespace App\Http\Middleware;

use App\Models\UserDevices;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StorePlayerId
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->has('playerId') || $request->input('playerId') == null) {
            return $next($request);
        }

        $playerId = $request->input('playerId');

        // if user is logged in
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return $next($request);
        }

        // if record exists, update it
        $user_device = UserDevices::where('user_id', $user->id)->first();
        if ($user_device) {
            $user_device->player_id = $playerId;
            $user_device->save();
            return $next($request);
        }

        // if record is not exists, create it
        $user_device = new UserDevices();
        $user_device->user_id = $user->id;
        $user_device->player_id = $playerId;
        $user_device->save();

        return $next($request);
    }
}
