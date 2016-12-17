<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\ChatMessage;

class ChatMessageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test API tokens
     */
    const USER_A_TEST_TOKEN = 'auNb4rwZph52';
    const USER_B_TEST_TOKEN = '4HkXiQiymhZR';

    const USER_TYPE_REPRESENTATIVE = 'representative';
    const USER_TYPE_CUSTOMER = 'customer';

    const CONVERSATION_ID = 1;

    /**
     * Test user
     *
     * @var User
     */
    protected $userA;

    /**
     * Test user
     *
     * @var User
     */
    protected $userB;

    /**
     * Test message from user A
     *
     * @var ChatMessage
     */
    protected $messageA;

    /**
     * Test message from user B
     *
     * @var ChatMessage
     */
    protected $messageB;

    public function setUp()
    {
        parent::setUp();

        $this->userA = factory(App\User::class)->create([
            'name' => 'Mauricio Cuenca',
            'email' => 'mauricio@cuenca.com',
            'api_token' => self::USER_A_TEST_TOKEN,
            'type' => self::USER_TYPE_REPRESENTATIVE,
        ]);

        $this->userB = factory(App\User::class)->create([
            'name' => 'Customer Rep.',
            'email' => 'rep@cuenca.com',
            'api_token' => self::USER_B_TEST_TOKEN,
            'type' => self::USER_TYPE_CUSTOMER,
        ]);

        $this->messageA = factory(App\ChatMessage::class)->create([
            'conversation_id' => self::CONVERSATION_ID,
            'sender_id' => $this->userA->id,
            'receiver_id' => $this->userB->id,
            'sender_name' => $this->userA->name,
            'receiver_name' => $this->userB->name,
            'message' => 'Message sent by Mauricio',
        ]);

        $this->messageB = factory(App\ChatMessage::class)->create([
            'conversation_id' => self::CONVERSATION_ID,
            'sender_id' => $this->userB->id,
            'receiver_id' => $this->userA->id,
            'sender_name' => $this->userB->name,
            'receiver_name' => $this->userA->name,
            'message' => 'Message sent by Customer Rep.',
        ]);
    }

    /**
     * Test getting the all chats owned by each user.
     */
    public function testGetChatsForAuthenticatedUser()
    {
        $this->actingAs($this->userA, 'api');
        $this->json('GET', sprintf('/api/v1/chats?api_token=%s', self::USER_A_TEST_TOKEN))
            ->seeJson([
                'conversation_id' => (string)self::CONVERSATION_ID,
                'messages' => '2',
            ]);

        $this->actingAs($this->userB, 'api');
        $this->json('GET', sprintf('/api/v1/chats?api_token=%s', self::USER_B_TEST_TOKEN))
            ->seeJson([
                'conversation_id' => (string)self::CONVERSATION_ID,
                'messages' => '2',
            ]);
    }

    /**
     * Test getting a conversation owned by the user.
     */
    public function testGetConversationForAuthenticatedUser()
    {
        $this->actingAs($this->userA, 'api');
        $this->json('GET', sprintf('/api/v1/chats/%d?api_token=%s', self::CONVERSATION_ID, self::USER_A_TEST_TOKEN))
            ->seeJson([
                'id' => (string)$this->userA->id,
                'message' => 'Message sent by Mauricio',
            ]);

        $this->actingAs($this->userB, 'api');
        $this->json('GET', sprintf('/api/v1/chats/%d?api_token=%s', self::CONVERSATION_ID, self::USER_B_TEST_TOKEN))
            ->seeJson([
                'id' => (string)$this->userB->id,
                'message' => 'Message sent by Customer Rep.',
            ]);
    }

    /**
     * Test getting a conversation ID owned by a different user.
     */
    public function testGetNonExistingConversationById()
    {
        // User A trying to retrieve user's B note should throw an 404 HTTP response
        $this->actingAs($this->userA, 'api');
        $response = $this->call('GET', sprintf('/api/v1/chats/%d', 2), ['api_token' => self::USER_A_TEST_TOKEN]);
        $this->assertEquals(Illuminate\Http\Response::HTTP_OK, $response->getStatusCode());
        $this->assertEmpty(json_decode($response->getContent(), true));
    }

    public function testModifyUserName()
    {
        $this->actingAs($this->userA, 'api');
        $this->json(
            'PUT',
            sprintf('/api/v1/users/1?api_token=%s', self::USER_A_TEST_TOKEN),
            [
                'name' => 'New Name',
            ])
            ->seeJson([
                'name' => 'New Name',
            ]);
    }

    /**
     * Test trying to modify a user's name without proper permissions
     */
    public function testModifyUserNameByUnauthorizedUser()
    {
        $this->actingAs($this->userB, 'api');
        $response = $this->call(
            'PUT',
            sprintf('/api/v1/users/1?api_token=%s', self::USER_A_TEST_TOKEN),
            ['name' => 'New Name']
        );
        $this->assertEquals(Illuminate\Http\Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertEmpty(json_decode($response->getContent(), true));
    }

    /**
     * Test whether a conversation can be deleted
     */
    public function testDeleteConversation()
    {
        $response = $this->call(
            'DELETE',
            sprintf('/api/v1/chats/%d', self::CONVERSATION_ID),
            [
                'api_token' => self::USER_A_TEST_TOKEN
            ]
        );
        $this->assertEmpty(json_decode($response->getContent()));
    }
}
