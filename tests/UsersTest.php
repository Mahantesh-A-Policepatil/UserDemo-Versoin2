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
        $this->token = $response->original['api_key'];
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('success', $response->original['status']);
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
     /*
    public function generateCode($limit){
        $code = '';
        for($i = 0; $i < $limit; $i++) { 
            $code .= mt_rand(0, 9); 
        }
        return $code;
    }
   
    public function testCreateUser()
    {
        //$this->Login('mahantesh@gmail.com', 'Shakti@123');
        $newPassword =Hash::make(Str::random(8));
        $userName = Str::random(8);
        $address = Str::random(25);
        //echo $this->generateCode(10); exit;
        $parameters = [
            'username' => $userName,
            'password' => $newPassword,
            'email' => $userName."@gmail.com",
            'mobile' => $this->generateCode(10),
            'address' => $address
        ];
        $response = $this->post("http://localhost:8000/api/v1/users/register", $parameters, []);
        //$response = $this->post('POST', 'http://localhost:8000/api/v1/users/register', $parameters, ['HTTP_Authorization' => "bearer ".$this->token]);
        //$this->assertEquals(200, $response->getStatusCode());
        $this->seeStatusCode(200);
        $latestUser = User::latest()->first();
        //print_r($latestUser['username']);
        //print_r($response);
        $this->assertEquals($parameters['username'], $latestUser['username']);
        
        
    }
    */

    
}