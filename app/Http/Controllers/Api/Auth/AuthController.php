<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\GeneralHelper;
use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\CreateSessionRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Models\User;
use App\Http\Modules\UserModule;

use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    /**
     * The service name.
     * 
     * @var String
     */
    protected $name = 'auth service';

    /**
     * User module.
     * 
     * @var UserModule
     */
    private $module;

    public function __construct()
    {
        $this->module = new UserModule(new User);
    }

    /**
     * Create session token for the client.
     * 
     * @param CreateSessionRequest request
     * @return \Illuminate\Http\Response response
     */
    public function createSession(CreateSessionRequest $request)
    {
        $validated = $request->validated();
        $email = $validated['email'];
        $password = $validated['password'];

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->throwError(401);
        }

        $user = Auth::user();
        $response = [
            'user' => $user,
            'token' => $user->createToken('userToken')->plainTextToken,
            'type' => 'Bearer'
        ];
        return $this->sendResponse($response);
    }

    /**
     * Revoke session token.
     * 
     * @param
     * @return \Illuminate\Http\Response
     */
    public function revokeSession()
    {
        $user = Auth::user();
        if ($user) {
            $user->currentAccessToken()->delete();
            return $this->sendResponse(null);
        }

        $this->throwError(400);
    }

    /**
     * Create new user for this application.
     * 
     * @param SignUpRequest request
     * @return \Illuminate\Http\Response response
     */
    public function signUp(SignUpRequest $request)
    {
        $validated = $request->validated();

        // - start trx
        $this->startTrx();
        try {
            $this
                ->module
                ->create($validated);

            // - commit
            $this->commitTrx();
        } catch (\Exception $e) {
            // rollback
            $this->rollbackTrx();
            $this->throwError(400, $e->getMessage());
        } 

        return $this->sendResponse(null, 201);
    }
}
