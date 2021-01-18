<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Groups;


class GroupTest extends TestCase
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

    public function testGetGroups()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/groups?group_name=',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'group_name',
                    'group_desc',
                    'group_owner_id',
                    'is_public_group',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);
    }

    public function testGetGroupById()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/groups/1',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'group_name',
                    'group_desc',
                    'group_owner_id',
                    'is_public_group',
                    'created_at',
                    'updated_at'
                ]
            ]    
        );
    }
         
    public function testCreateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = [
            'group_name' => Str::random(8),
            'is_public_group' => 1,
            'group_desc' => Str::random(25),
        ];
        $response = $this->post("http://localhost:8000/api/v1/groups", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
      
        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'group_name',
                    'group_desc',
                    'group_owner_id',
                    'is_public_group',
                    'created_at',
                    'updated_at'
                ]
            ]    
        );
        
    }

    public function testUpdateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
       
        $parameters = [
            'group_name' => "Group-10",
            'is_public_group' => 1,
            'group_desc' => "Group-10-".Str::random(25),
        ];
        $response = $this->put("http://localhost:8000/api/v1/groups/update/14", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeJsonStructure(
            ['data' =>
                [
                    'id',
                    'group_name',
                    'group_desc',
                    'group_owner_id',
                    'is_public_group',
                    'created_at',
                    'updated_at'
                ]
            ]    
        );
       
    }
    /*
    public function testDeleteGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/groups/delete/10", [], ['HTTP_Authorization' => "bearer $this->token"]);

        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }
    */

    public function testJoinPublicGroup()
    {
        $this->Login('6KbHTDzc@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/join", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
      
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    public function testLeavePublicGroup()
    {
        $this->Login('6KbHTDzc@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/leave", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
      
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    public function testAddUserToPrivateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 100];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/add", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
      
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    public function testRemoveUserFromPrivateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 100];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/remove", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
 
        $this->seeStatusCode(200);
      
        $this->seeJsonStructure([
            'status',
            'message'
        ]);
    }

    public function testGetGroupMembers()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token); 
        $response = $this->get('http://localhost:8000/api/v1/groupMembers/?group_name=Mahantesh-Public-Group',['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        
        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'username',
                    'updated_at',
                    'updated_at'
                ]
            ]
        ]);
    }
    
}