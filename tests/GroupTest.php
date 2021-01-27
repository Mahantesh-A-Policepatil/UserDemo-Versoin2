<?php
namespace tests;

use App\Group;
use App\User;
use Illuminate\Support\Facades\Config;
//use Config;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use DB;


class GroupTest extends TestCase
{

    public $token;
    public $groupId;
    public $publicGroup;
    public $privateGroup;
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
     * Creates a Public Group
     * @return void
     */
    public function createPublicGroup()
    {
        $parameters = [
            'group_name' => "Mahantesh-Public-Group-" . Str::random(5),
            'is_public_group' => 1,
            'group_desc' => "Hello Folks this is Mahantesh Public Group",
        ];
        $response = $this->post("http://localhost:8000/api/v1/groups", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        return $response;
    }

    /**
     * Helper function
     * Creates a Private Group
     * @return void
     */
    public function createPrivateGroup()
    {
        $parameters = [
            'group_name' => "Mahantesh-Private-Group-" . Str::random(5),
            'is_public_group' => 0,
            'group_desc' => "Hello Folks this is Mahantesh Private Group",
        ];
        $response = $this->post("http://localhost:8000/api/v1/groups", $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        return $response;
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
            'email' => $userName."@gmail.com",
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
        $this->publicGroup = $this->createPublicGroup();
        $this->privateGroup = $this->createPrivateGroup();
        $this->newUser = $this->createUser();
    }

    /**
     * A example for creating a new group,
     * It should return Status Code : 200
     * @return void
     */
    public function testcreatePublicGroup()
    {

        $parameters = [
            'group_name' => "India-" . "Mahantesh-" . Str::random(5),
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
     * It should return Status Code : 422
     * @return void
     */
    public function testcreatePublicGroupValidationTest()
    {
        //$this->testLogin('mahantesh@gmail.com', 'Shakti@123');
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
        //$this->publicGroup
        $response = $this->publicGroup;
        $group_name = $response['data']['group_name'];
        $response = $this->get("http://localhost:8000/api/v1/groups?group_name=" . $group_name, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];
        $response = $this->get('http://localhost:8000/api/v1/groups/' . $groupId, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
        $response = $this->get('http://localhost:8000/api/v1/groups/99000000', ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(404);

    }

    /**
     * A example for updating an existing group
     * It should return Status Code : 200
     * @return void
     */
    public function testUpdateGroup()
    {
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];
        $parameters = [
            'group_name' => "India-" . "Mahantesh-" . Str::random(5),
            'is_public_group' => 1,
            'group_desc' => "India-" . "Mahantesh-" . Str::random(5),
        ];
        $baseUrl = "http://localhost:8000/api/v1/groups/" . $groupId;

        $response = $this->put($baseUrl, $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];
        $parameters = [
            'group_name' => "",
            'is_public_group' => "",
            'group_desc' => "",
        ];
        $response = $this->put("http://localhost:8000/api/v1/groups/".$groupId, $parameters, ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();
        $this->seeStatusCode(422);
    }

    /**
     * A example where en existing user is trying to join the same group again
     * It should return Status Code : 409
     * @return void
     */
    public function testJoinPublicGroupUserAlreadyExist()
    {
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response = $this->newUser;
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/".$groupId."/join", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        $this->seeStatusCode(409);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }

    public function testJoinPublicGroup()
    {

        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();

        $jwtToken = JWTAuth::fromUser($user);
        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/".$groupId."/join", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        if($response['status'] != 409){
            $this->seeStatusCode(200);
        }

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
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/join", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();
        if($response['status'] != 409){
            $this->seeStatusCode(401);
        }
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
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response =$this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/leave", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);
        $userData = [
            'group_id' => $groupId,
            'user_email' => $userEmail
        ];
        return $userData;
    }

     /**
     * A example for user to leave a public group
     * It should return Status Code : 409
     * @return void
     */
    public function testLeavePublicGroupAgain()
    {
        $userData = $this->testLeavePublicGroup();

        //$response = $this->publicGroup;
        $groupId = $userData['group_id'];

        //$response = $this->createUser();
        $userEmail = $userData['user_email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = [];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/leave", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = ['user_id' =>  $user->id];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/add", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = ['user_id' => $user->id];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/add", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

        $this->seeStatusCode(200);

        $this->seeJsonStructure([
            'status',
            'message',
        ]);

        return $user->id;
    }

    /**
     * A example where owner of the group can add the user again to his group
     * It should return Status Code : 409
     * @return void
     */
    public function testAddUserToPrivateGroupAgain()
    {
        $addedUser = $this->testAddUserToPrivateGroup();
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = ['user_id' => $addedUser];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/add", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        $addedUser = $this->testAddUserToPrivateGroup();

        $response = $this->publicGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = ['user_id' => 5];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/remove", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        $addedUser = $this->testAddUserToPrivateGroup();
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $parameters = ['user_id' => $addedUser];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/remove", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        $addedUser = $this->testAddUserToPrivateGroup();
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $response = $this->createUser();
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);
        $parameters = ['user_id' => $addedUser];
        $response = $this->post("http://localhost:8000/api/v1/groups/" . $groupId . "/remove", $parameters, ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();
        if($response['status'] != 200){
            $this->seeStatusCode(404);
        }
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
        $response = $this->privateGroup;
        $groupName = $response['data']['group_name'];
        $response = $this->get("http://localhost:8000/api/v1/groupMembers/?group_name=$groupName", ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

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
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $response = $this->newUser;
        $userEmail = $response['data']['email'];

        $user = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($user);

        $response = $this->delete("http://localhost:8000/api/v1/groups/".$groupId, [], ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();

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
        //$this->testLogin('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->privateGroup;
        $groupId = $response['data']['id'];

        $groupOwners = Group::select('group_owner_id')->distinct()->pluck('group_owner_id');
        $user = User::whereNotIn('id', $groupOwners)->first();
        //echo "groupId ".$groupId." userId ". $user->id." userEmail ". $user->email; exit;
        $userEmail = $user->email;
        $userData = User::where('email', $userEmail)->first();
        $jwtToken = JWTAuth::fromUser($userData);

        $response = $this->delete("http://localhost:8000/api/v1/groups/".$groupId, [], ['HTTP_Authorization' => "bearer $jwtToken"])->response->getOriginalContent();
        if($response['status'] != 410){
            $this->seeStatusCode(401);
        }
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
        //$this->testLogin('mahantesh@gmail.com', 'Shakti@123');
        $response = $this->delete("http://localhost:8000/api/v1/groups/990000000", [], ['HTTP_Authorization' => "bearer $this->token"])->response->getOriginalContent();

        $this->seeStatusCode(404);
        $this->seeJsonStructure([
            'status',
            'message',
        ]);
    }




}
