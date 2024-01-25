<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {
        try {
            auth()->login(User::findOrFail($request->id));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'user not found'], 404);
        }

        $user = auth()->user();

        if (! hash_equals((string) $user->getKey(), (string) $request->id)) {
            return response()->json(['message' => 'forbidden'], 403);
        }

        if (! hash_equals(sha1($user->getEmailForVerification()), (string) $request->hash)) {
            return response()->json(['message' => 'forbidden'], 403);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(
                config('app.frontend_url').RouteServiceProvider::HOME.'?verified=1'
            );
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(
            config('app.frontend_url').RouteServiceProvider::HOME.'?verified=1'
        );
    }
}
