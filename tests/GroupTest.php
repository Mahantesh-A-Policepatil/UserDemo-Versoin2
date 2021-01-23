<?php

use App\Groups;
use Illuminate\Support\Str;

class GroupTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public $token;

    public function Login($email = false, $password = false)
    {
        if (!$email) {
            $email = 'mahantesh@gmail.com';
        }
        if (!$password) {
            $password = 'Shakti@123';
        }
        $response = $this->call('POST', '/http://localhost:8000/api/v1/users/login', [
            'email' => $email,
            'password' => $password,
        ]);
        //print_r($response->original); exit;
        //$this->token = $response->original['api_key'];
        $this->token = $response->original['access_token'];
        $this->assertEquals(200, $response->getStatusCode());
        //$this->assertEquals('success', $response->original['status']);
    }

    /**
     * A example for creating a new group,
     * It should return Status Code : 200
     * @return void
     */
    public function testCreateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = [
            'group_name' => "India-" . "Mahantesh-" . Str::random(8),
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
                    'updated_at',
                ],
            ]
        );
    }

    /**
     * A example for creating a new group, when mandatory fields are missing,
     * It should return Status Code : 200
     * @return void
     */
    public function testCreateGroupValidationTest()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = [
            'group_name' => "",
            'is_public_group' => 1,
            'group_desc' => "",
        ];
        $response = $this->post("http://localhost:8000/api/v1/groups", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(422);
    }
    /**
     * A example for fetching all groups,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetGroups()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groups?group_name=', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * A example for fetching all groups with Filter,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetGroupsWithFilter()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groups?group_name=Group-10', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * A example for fetching group for a given group_id,
     * It should return Status Code : 200,
     * @return void
     */
    public function testGetGroupById()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groups/1', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
                    'updated_at',
                ],
            ]
        );
    }

    /**
     * A example for fetching group for a given group_id(invalid group)id),
     * It should return Status Code : 404,
     * @return void
     */
    public function testGetGroupByInvalidUserId()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groups/233', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(404);

    }
    /**
     * A example for updating an existing group
     * It should return Status Code : 200
     * @return void
     */
    public function testUpdateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');

        $parameters = [
            'group_name' => "Group-10",
            'is_public_group' => 1,
            'group_desc' => "Group-10-" . Str::random(25),
        ];
        $response = $this->put("http://localhost:8000/api/v1/groups/1", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
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
                    'updated_at',
                ],
            ]
        );

    }

    /**
     * A example for updating an existing group, when mandatory fields are missing
     * It should return Status Code : 422
     * @return void
     */
    public function testUpdateGroupValidationTest()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');

        $parameters = [
            'group_name' => "",
            'is_public_group' => "",
            'group_desc' => "",
        ];
        $response = $this->put("http://localhost:8000/api/v1/groups/1", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(422);
    }

    /**
     * A example where en existing user is trying to join the same group again
     * It should return Status Code : 409
     * @return void
     */
    public function testJoinPublicGroupUserAlreadyExist()
    {
        $this->Login('1YRA2Yq0@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/join", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(409);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }
    /**
     * A example for user to join a public group
     * It should return Status Code : 200
     * @return void
     */
    public function testJoinPublicGroup()
    {
        $this->Login('mmaOuPJJ@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/join", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for user to join a private group
     * It should return Status Code : 401
     * @return void
     */
    public function testJoinPrivateGroup()
    {
        $this->Login('mmaOuPJJ@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/join", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(401);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for user to leave a public group
     * It should return Status Code : 200
     * @return void
     */
    public function testLeavePublicGroup()
    {
        $this->Login('mmaOuPJJ@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/leave", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for user to leave a public group
     * It should return Status Code : 409
     * @return void
     */
    public function testLeavePublicGroupAgain()
    {
        $this->Login('mmaOuPJJ@gmail.com', 'Shakti@123');
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/leave", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(409);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group trying to add a user to a public group
     * It should return Status Code : 401
     * @return void
     */
    public function testAddUserToPublicGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 6];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/add", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(401);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group can add a user to his group
     * It should return Status Code : 200
     * @return void
     */
    public function testAddUserToPrivateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/add", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group can add the user again to his group
     * It should return Status Code : 409
     * @return void
     */
    public function testAddUserToPrivateGroupAgain()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/add", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(409);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group trying to remove a user from public group
     * It should return Status Code : 401
     * @return void
     */
    public function testRemoveUserFromPublicGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/1/remove", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(401);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group can remove a user to his group
     * It should return Status Code : 200
     * @return void
     */
    public function testRemoveUserFromPrivateGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/remove", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example where owner of the group can remove the same user again from his group
     * It should return Status Code : 404
     * @return void
     */
    public function testRemoveUserFromPrivateGroupAgain()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/3/remove", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(404);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example of getting all group members for a given group_name
     * It should return Status Code : 200
     * @return void
     */
    public function testGetGroupMembers()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groupMembers/?group_name=Mahantesh-Public-Group', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'data' => ['*' =>
                [
                    'id',
                    'username',
                    'updated_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    /**
     * A example of getting all group members for a given group_name, testing validation error message,
     * It should return Status Code : 422
     * @return void
     */
    public function testGetGroupMembersWithoutGroupNameParameter()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        //print_r($this->token);
        $response = $this->get('http://localhost:8000/api/v1/groupMembers/?group_name=', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(422);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for Deleting an existing group when unauthorized user trying to delete a user,
     * It should return Status Code : 410,
     * CAUTION : Please provide existing group_id as an input in query string parameter i.e 97 needs to be changed
    */
    public function testDeleteGroup()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/groups/97", [], ['HTTP_Authorization' => "bearer $this->token"]);

        $this->seeStatusCode(410);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for Deleting an existing group when unauthorized user trying to delete a user,
     * It should return Status Code : 401,
     *
     */
    public function testDeleteGroupByUnauthorizedOwner()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/groups/20", [], ['HTTP_Authorization' => "bearer $this->token"]);

        $this->seeStatusCode(401);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    /**
     * A example for Deleting an existing group when group owner trying to delete a group that does not exists,
     * It should return Status Code : 404,
     *
     */
    public function testDeleteGroupThatDoesNotExists()
    {
        $this->Login('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/groups/206", [], ['HTTP_Authorization' => "bearer $this->token"]);

        $this->seeStatusCode(404);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

}
