<?php

namespace App\Api\V1\Http\Controllers\Auth;

use DB;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Exceptions\CustomValidationException;
use App\Api\V1\Http\Controllers\ApiController;
use App\Api\V1\Http\Controllers\Auth\AuthenticateUser;
use App\Api\V1\Http\Requests\Auth\RegistrationRequest;

class RegistrationController extends ApiController
{
    use AuthenticateUser;
    /**
     * Register the user account.
     *
     * @param RegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws CustomValidationException
     * @throws Exception
     */
    public function register(RegistrationRequest $request)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');
       
        DB::beginTransaction();
        try {
            $data = [
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'mobile' => $request->input('mobile'),
                'password' => bcrypt($request->input('password')),
                'active' => true,
                'address_line_1' => $request->input('address_line_1', Null),
                'address_line_2' => $request->input('address_line_2', Null),
            ];

            $user = User::create($data);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            $this->logException($e);

            throw $e;
        }

        $customRequest = new Request([
            'username' => $mobile,
            'password' => $password,
        ]);

        return $this->issueToken($customRequest);
    }

}
