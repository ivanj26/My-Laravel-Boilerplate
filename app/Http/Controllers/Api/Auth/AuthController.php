<?php

namespace App\Http\Controllers\Api\Auth;

use App\Helper\ServiceCallerHelper;
use App\Http\Controllers\Api\BaseController;
use App\Http\Modules\NotificationTemplateModule;
use App\Http\Requests\Auth\CreateSessionRequest;
use App\Http\Requests\Auth\SignUpRequest;
use App\Http\Modules\UserModule;
use App\Services\CreateRandomTokenService;
use GuzzleHttp\Promise\Promise;
use Illuminate\Http\JsonResponse;
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
            $this->throwError(JsonResponse::HTTP_UNAUTHORIZED, 'failed to issue the token');
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

        $this->throwError(JsonResponse::HTTP_BAD_REQUEST);
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
            $token = CreateRandomTokenService::generate();
            $validated['verify_token'] = $token;

            $id = $this
                ->module
                ->create($validated);

            // - commit
            $this->commitTrx();

            // - send notification
            $user = $this->module->findOneBy('id', $id);
            $body = [
                'data' => [
                    'fullName' => data_get($user, 'name'),
                    'email' => data_get($user, 'email'),
                    'appName' => env('APP_NAME'),
                    'token' => $token
                ],
                'name' => 'email-user-registration',
                'to' => data_get($validated, 'email'),
                'cc' => []
            ];

            $promise = new Promise(function() use(&$promise, $body, $id) {
                Auth::loginUsingId($id);

                $res = ServiceCallerHelper::call('POST', '/api/v1/notifications/email/send', $body);
                $promise->resolve($res);
            });
            $promise->wait();
        } catch (\Exception $e) {
            // rollback
            $this->rollbackTrx();
            $this->throwError(JsonResponse::HTTP_BAD_REQUEST, $e->getMessage());
        }

        return $this->sendResponse(null, JsonResponse::HTTP_CREATED);
    }
}
