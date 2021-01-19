<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;


class UserTest extends TestCase
{
    
    /**
     * A basic test example for successful Login,
     * It should return Status Code : 200,
     * @return void
     */
    public $token;
  
    public function Login($email=false, $password=false)
    {
        if(!$email)
        {
            $email = 'mahantesh@gmail.com';
        }
        if(!$password)
        {
            $password = 'Shakti@123';
        }
        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password
        ]);
        //$this->token = $response->original['api_key'];
        $this->token = $response->original['access_token'];
        $this->assertEquals(200, $response->getStatusCode());
        //$this->assertEquals('success', $response->original['status']);
    }

    /**
     * A basic test example for Login with wrong credentials,
     * It should return Status Code : 401,
     * @return void
     */
    public function loginWithWrongCredentials()
    {
        $email = 'mahantesh1234@gmail.com';
        $password = 'Shakti@123';
       
        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

     /**
     * A basic test example for Login : When user does not enter Email,
     * It should return Status Code : 422,
     * @return void
     */
    public function loginWithEmailFieldValidation()
    {
        $email = '';
        $password = 'Shakti@123';
       
        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password
        ]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * A basic test example for Login : When user does not enter Password,
     * It should return Status Code : 422,
     * @return void
     */
    public function loginWithPasswordFieldValidation()
    {
        $email = 'mahantesh1234@gmail.com';
        $password = '';
       
        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password
        ]);
        $this->assertEquals(422, $response->getStatusCode());
    }

    /**
     * A example for fetching all users,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUsers()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/users?name=',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
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
                    'updated_at'
                ]
            ]
        ]);
    }

    /**
     * A example for fetching all users with Filter,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUsersWithFilter()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/users?name=maha',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
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
                    'updated_at'
                ]
            ]
        ]);
    }

    /**
     * A example for fetching user for a given user_id,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetUserById()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/users/1',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
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
                    'updated_at'
                ]
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
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/users/1000',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(404);
        
    }
     
    public function generateCode($limit){
        $code = '';
        for($i = 0; $i < $limit; $i++) { 
            $code .= mt_rand(0, 9); 
        }
        return $code;
    }
    /**
     * A example for registering a new user,
     * It should return Status Code : 200
     * @return void
     */
    public function testRegisterUser()
    {
        $newPassword = "India@123";
        $userName = "India".Str::random(8);
        $address = "India".Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => $userName."@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, [])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
        /*
        $latestUser = User::latest()->first();
        $this->assertEquals($parameters['username'], $latestUser['username']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'username',
                    'email',
                    'mobile',
                    'address',
                    'created_at',
                    'updated_at'
                ]
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
            'address' => $address
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, [])->response->getOriginalContent();
 
        $this->seeStatusCode(422);
        //print_r($response); 
        /*
        $latestUser = User::latest()->first();
        $this->assertEquals($parameters['username'], $latestUser['username']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */
       
        
    }
    /**
     * A example for updating an existing user
     * It should return Status Code : 200
     * @return void
     */
    public function testUpdateUser()
    {
        $this->Login('rajesh@gmail.com', 'Shakti@123');
        $newPassword = "Shakti@123";
        $userName = "Mahesh Patil";
        $address = "India".Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "rajesh@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/update/1", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
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
                    'updated_at'
                ]
            ]    
        );
        /*
        $latestUser = User::find(8);
    
        $this->assertEquals($parameters['username'], $latestUser['username']);
        //$this->assertEquals(Hash::make($parameters['password']), $latestUser['password']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */        
    }

    /**
     * A example for updating an existing user when mandatory fields are missing,
     * It should return Status Code : 422
     * @return void
     */
    public function testUpdateUserValidationTest()
    {
        $this->Login('rajesh@gmail.com', 'Shakti@123');
        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/update/1", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(422);
        
        /*
        $latestUser = User::find(8);
    
        $this->assertEquals($parameters['username'], $latestUser['username']);
        //$this->assertEquals(Hash::make($parameters['password']), $latestUser['password']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */        
    }

    /**
     * A example for updating an existing user when unauthorized user trying to update a user,
     * It should return Status Code : 401
     * @return void
     */
    public function testUpdateUserByUnauthorizedUser()
    {
        $this->Login('rajesh@gmail.com', 'Shakti@123');
        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/update/2", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(401);
        
        /*
        $latestUser = User::find(8);
    
        $this->assertEquals($parameters['username'], $latestUser['username']);
        //$this->assertEquals(Hash::make($parameters['password']), $latestUser['password']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */        
    }

    /**
     * A example for Updating user for a inavlid user_id : i.e user who is not registered with us,
     * user_id : 100 is not there in our database,
     * It should return Status Code : 404
     * @return void
     */
    public function testUpdateUserInavlidUserId()
    {
        $this->Login('rajesh@gmail.com', 'Shakti@123');
        $newPassword = "";
        $userName = "";
        $address = "";
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "",
            'mobile' => "",
            'address' => $address
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/update/1000", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(404);
        
        /*
        $latestUser = User::find(8);
    
        $this->assertEquals($parameters['username'], $latestUser['username']);
        //$this->assertEquals(Hash::make($parameters['password']), $latestUser['password']);
        $this->assertEquals($parameters['email'], $latestUser['email']);
        $this->assertEquals($parameters['mobile'], $latestUser['mobile']);
        $this->assertEquals($parameters['address'], $latestUser['address']);
        */        
    }

    /**
     * A example for Deleting an existing user when unauthorized user trying to delete a user,
     * It should return Status Code : 401,
     * CAUTION : Please create auser then run this test case.
    
    public function testDeleteUserInavlidUserId()
    {
        $this->Login('monty@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/users/delete/1", [], ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(401);
    }
     */
    /**
     * A example for delete an existing user.
     * It should return Status Code : 410,
     * CAUTION : Please create auser then run this test case.
    
    public function testDeleteUser()
    {
        $this->Login('monty@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/users/delete/82", [], ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
     */
    
    

    
}