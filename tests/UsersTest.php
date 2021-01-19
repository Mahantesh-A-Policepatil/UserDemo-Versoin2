<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;


class UserTest extends TestCase
{
    
    /**
     * A basic test example.
     *
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
     
    public function generateCode($limit){
        $code = '';
        for($i = 0; $i < $limit; $i++) { 
            $code .= mt_rand(0, 9); 
        }
        return $code;
    }
   
    public function testRegisterUser()
    {
        $newPassword =Str::random(8);
        $userName = Str::random(8);
        $address = Str::random(25);
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

    public function testUpdateUser()
    {
        $this->Login('rajesh@gmail.com', 'Shakti@123');
        $newPassword = "Shakti@123";
        $userName = "Mahesh Patil";
        $address = Str::random(25);
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => "rajesh@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address
        ];
        $response = $this->put("http://localhost:8000/api/v1/users/update/1", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
    /*
    public function testDeleteUser()
    {
        $this->Login('xyztt@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/users/delete/109", [], ['HTTP_Authorization' => "bearer $this->token"]);

        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
    */
    

    
}