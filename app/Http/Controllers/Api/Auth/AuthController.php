<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Modules\NotificationTemplateModule;
use App\Http\Requests\Auth\CreateSessionRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Modules\UserModule;
use App\Notifications\User\NewUserRegisteredNotification;
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

    /**
      * NotificationTemplate module.
      * 
      * @var NotificationTemplateModule
      */
    private $templateModule;


    public function __construct()
    {
        $this->module = new UserModule();
        $this->templateModule = new NotificationTemplateModule();
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
        $email = data_get($validated, 'email');
        $password = data_get($validated, 'password');

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            $this->throwError(401, 'failed to issue the token');
        }

        $user = Auth::user();
        $response = [
            'user' => $user,
            'token' => $user->createToken('auth_token', [data_get($user, 'role')])->plainTextToken,
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
            $id = $this
                ->module
                ->create($validated);

            // - commit
            $this->commitTrx();

            // - send notification
            $user = $this->module->findOneBy('id', $id);
            $template = $this->templateModule
                ->findOneBy('name', 'email-user-registration');
            $data = [
                'fullName' => $user->name,
            ];

            $user->notify(new NewUserRegisteredNotification($data, $template));
        } catch (\Exception $e) {
            // rollback
            $this->rollbackTrx();
            $this->throwError(400, $e->getMessage());
        } 

        return $this->sendResponse(null, 201);
    }
}
