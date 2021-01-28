<?php

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsersTest extends TestCase
{
    public $token;
    public $newUser;

    public function generateCode($limit)
    {
        $code = '';
        for ($i = 0; $i < $limit; $i++) {
            $code .= mt_rand(0, 9);
        }
        return $code;
    }

    /**
     * Helper function
     * Creates a new user
     * @return void
     */
    public function createUser()
    {
        $newPassword = "Shakti@123";
        $userName = "India" . Str::random(8);
        $address = "India" . Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => $userName . "@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address,
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, [])->response->getOriginalContent();
        return $response;
    }

    public function setUp(): void
    {
        parent::setup();
        $user = User::where('email', 'mahantesh@gmail.com')->first();
        $this->token = JWTAuth::fromUser($user);
        $this->newUser = $this->createUser();
    }

    /**
     * A basic test example for successful login,
     * It should return Status Code : 200,
     * @return void
     */
    public function testLoginDemo($email = false, $password = false)
    {
        if ($email === false) {
            $email = 'mahantesh@gmail.com';
            $password = 'Shakti@123';
        }

        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password,
        ]);
        $this->seeStatusCode(200);
        $this->token = $response->original['access_token'];
        return $this->token;

    }

    /**
     * A basic test example for login with wrong credentials,
     * It should return Status Code : 401,
     * @return void
     */
    public function testLoginWithWrongCredentials()
    {
        $email = 'mahantesh1234@gmail.com';
        $password = 'Shakti@123';

        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password,
        ]);
        //$this->assertEquals(401, $response->getStatusCode());
        $this->seeStatusCode(401);
    }

    /**
     * A basic test example for login : When user does not enter Email,
     * It should return Status Code : 422,
     * @return void
     */
    public function testLoginWithEmailFieldValidation()
    {
        $email = '';
        $password = 'Shakti@123';

        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password,
        ]);
        //$this->assertEquals(422, $response->getStatusCode());
        $this->seeStatusCode(422);
    }

    /**
     * A basic test example for login : When user does not enter Password,
     * It should return Status Code : 422,
     * @return void
     */
    public function testLoginWithPasswordFieldValidation()
    {
        $email = 'mahantesh1234@gmail.com';
        $password = '';

        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password,
        ]);
        //$this->assertEquals(422, $response->getStatusCode());
        $this->seeStatusCode(422);
    }

    /**
     * A example for fetching all users,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUsers()
    {
        $response = $this->get('http://localhost:8000/api/v1/users?name=', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * A example for fetching all users with Filter,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUsersWithFilter()
    {
        $response = $this->newUser;
        $userName = $response['data']['username'];
        $response = $this->get('http://localhost:8000/api/v1/users?name=' . $userName, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * A example for fetching user for a given user_id,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUserById()
    {
        $response = $this->newUser;
        $userId = $response['data']['id'];

        $response = $this->get('http://localhost:8000/api/v1/users/' . $userId, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at',
                ],
            ]
        );
    }

    /**
     * A example for fetching user for a inavlid user_id : i.e user who is not registered with us,
     * user_id : 100 is not there in our database,
     * It should return Status Code : 404
     * @return void
     */
    public function testGetUserByInvalidUserId()
    {
        $response = $this->get('http://localhost:8000/api/v1/users/100000', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(404);
    }

    /**
     * A example for registering a new user,
     * It should return Status Code : 200
     * @return void
     */
    public function testRegisterUser()
    {
        $newPassword = "Shakti@123";
        $userName = "India" . Str::random(8);
        $address = "India" . Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => $userName . "@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address,
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, [])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at',
                ],
            ]
        );

    }

    /**
     * A example for registering a new user when mandatory fields are missing,
     * It should return Status Code : 422
     * @return void
     */
    public function testRegisterUserValidationTest()
    {
        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address,
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, [])->response->getOriginalContent();

        $this->seeStatusCode(422);

    }
    /**
     * A example for updating an existing user
     * It should return Status Code : 200
     * @return void
     */
    public function testUpdateUser()
    {
        $response = $this->newUser;
        $userId = $response['data']['id'];

        $user = User::where('email', $response['data']['email'])->first();
        $jwtToken = JWTAuth::fromUser($user);

        $newPassword = "Shakti@123";
        $userName = "Mahantesh" . Str::random(5);
        $address = "India" . Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => $userName . "@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address,
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/" . $userId, $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at',
                ],
            ]
        );

    }

    /**
     * A example for updating an existing user when mandatory fields are missing,
     * It should return Status Code : 422
     * @return void
     */
    public function testUpdateUserValidationTest()
    {
        $response = $this->newUser;
        $userId = $response['data']['id'];

        $user = User::where('email', $response['data']['email'])->first();
        $jwtToken = JWTAuth::fromUser($user);

        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address,
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/" . $userId, $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();
        $this->seeStatusCode(422);

    }

    /**
     * A example for updating an existing user when unauthorized user trying to update a user,
     * It should return Status Code : 401
     * @return void
     */
    public function testUpdateUserByUnauthorizedUser()
    {
        $response = $this->newUser;
        $userData = User::where('id', '>', 1)->first();

        $user = User::where('email', $response['data']['email'])->first();
        $jwtToken = JWTAuth::fromUser($user);

        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address,
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/" . $userData->id, $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();
        $this->seeStatusCode(401);

    }

    /**
     * A example for Updating user for a inavlid user_id : i.e user who is not registered with us,
     * user_id : 100 is not there in our database,
     * It should return Status Code : 404
     * @return void
     */
    public function testUpdateUserInavlidUserId()
    {
        //$this->testLogin('rajesh@gmail.com', 'Shakti@123');
        $user = User::where('email', 'rajesh@gmail.com')->first();
        $this->token = JWTAuth::fromUser($user);
        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address,
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/9900000", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(404);

    }

    /**
     * A example for Deleting an existing user when unauthorized user trying to delete a user,
     * It should return Status Code : 401,
     * CAUTION : Please create auser then run this test case.
     */
    public function testDeleteUserInavlidUserId()
    {
        $user = User::where('email', 'IndiayJSC0R5i@gmail.com')->first();
        $this->token = JWTAuth::fromUser($user);
        $response = $this->delete("http://localhost:8000/api/v1/users/1", [], ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(401);
    }

    /**
     * A example for delete an existing user.
     * It should return Status Code : 410,
     * CAUTION : Please create auser then run this test case.
     */
    public function testDeleteUser()
    {
        $response = $this->newUser;
        $userEmail = $response['data']['email'];
        $userId = $response['data']['id'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);
        $response = $this->delete("http://localhost:8000/api/v1/users/" . $userId, [], ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for user logout,
     * It should return Status Code : 200,
     * @return void
     */
    public function testLogout()
    {
        $user = User::where('email', 'mahantesh@gmail.com')->first();
        $authToken = JWTAuth::fromUser($user);
        $response = $this->get('http://localhost:8000/api/v1/users/logout', ['HTTP_Authorization' => "bearer $authToken"])->response->getOriginalContent();
        $this->seeStatusCode(200);
        return $authToken;
    }

    /**
     * A example for same user trying to logout again,
     * It should return Status Code : 401,
     * @return void
     */
    public function testLogoutAgain()
    {
        $this->token = $this->testLogout();
        $response = $this->get('http://localhost:8000/api/v1/users/logout', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(401);
    }

}
