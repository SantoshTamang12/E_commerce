<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\User;
use App\Models\UserAdConversation;
use Illuminate\Http\Request;
use \Chat;
use App\Notifications\UserSendMessageNotification;

class ChatApiController extends Controller
{
    public function initiate(Request $request)
    {
        $user = auth('api')->user();
        $otherUser = User::findOrFail($request->user_id);
        $ad = Ad::findOrFail($request->ad_id);

        $participants = [$user, $otherUser, $ad];
        $userAdConversation = UserAdConversation::where([
            'ad_id' => $ad->id,
            'buyer_id' => $user->id,
            'seller_id' => $otherUser->id
        ])->first();

        if (!$userAdConversation) {
            $conversation = Chat::createConversation($participants)->makePrivate();
            $userAdConversation =  UserAdConversation::firstOrCreate([
                'ad_id' => $ad->id,
                'buyer_id' => $user->id,
                'seller_id' => $otherUser->id,
                'conversation_id' => $conversation->id
            ]);
        } else {
            $conversation = Chat::conversations()->getById($userAdConversation->conversation_id);
        }

        return response()->json([
            'status' => true,
            'data' => $conversation
        ], 200);
    }

    public function send(Request $request)
    {

        $user = auth('api')->user();
        $conversation = Chat::conversations()->getById($request->conversation_id);

        if ($conversation == null) {
            return response()->json([
                'status' => false,
                'message' => 'Conversation not found'
            ], 200);
        } 

        error_log('----' . $user->id);
        error_log('----' . $request->receiver_id);


        $receiver = User::findOrfail($request->receiver_id);

        $message = Chat::message($request->message)
            ->from($user)
            ->to($conversation)
            ->send();

        error_log('----' . $receiver->device_token);

        if($receiver->device_token){
            $receiver->notify(new UserSendMessageNotification($request->conversation_id, $conversation->getParticipants()[0]['title']));
        }


        return response()->json([
            'status' => true,
            'data' => $message
        ], 200);
    }

    public function fetch(Request $request)
    {
        $user = auth('api')->user();
        $conversation = Chat::conversations()->getById($request->conversation_id);

        if ($conversation == null) {
            return response()->json([
                'status' => false,
                'message' => 'Conversation not found'
            ], 200);
        }

        $messages = Chat::conversation($conversation)
            ->setParticipant($user)
            ->setPaginationParams([
                'page' => $request->page,
                'perPage' => 20,
                'sorting' => "desc",
                'columns' => [
                    'body'
                ],
            ])
            ->getMessages();

        return response()->json([
            'status' => true,
            'data' => $messages
        ], 200);
    }

    public function conversations(Request $request)
    {
        $user = auth('api')->user();

        // $conversations = Chat::conversations()
        //     ->setPaginationParams(['sorting' => 'desc'])
        //     ->setParticipant($user)
        //     ->limit(500)
        //     ->get();

        // foreach ($conversations as $key => $conversation) {
        //     $conversation['data'] = UserAdConversation::where([
        //         'conversation_id' => $conversation->id,
        //     ])->with('buyer:id,name,phone,avatar_url', 'seller:id,name,phone,avatar_url', 'ad:id,title', 'conversation')
        //         ->first();
        // }

        $buyingAdConversations = UserAdConversation::where([
            'buyer_id' => $user->id,
        ])
            ->join('chat_conversations', 'user_ad_conversations.conversation_id', '=', 'chat_conversations.id')
            ->with('buyer:id,name,phone,avatar_url', 'seller:id,name,phone,avatar_url', 'ad:id,title', 'conversation')
            ->orderBy('chat_conversations.updated_at', 'desc')
            ->get();

        $sellingAdConversations = UserAdConversation::where([
            'seller_id' => $user->id,
        ])
            ->join('chat_conversations', 'user_ad_conversations.conversation_id', '=', 'chat_conversations.id')
            ->with('buyer:id,name,phone,avatar_url', 'seller:id,name,phone,avatar_url', 'ad:id,title', 'conversation')
            ->orderBy('chat_conversations.updated_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                // 'conversations' => $conversations
                'buying' => $buyingAdConversations,
                'selling' => $sellingAdConversations
            ]
        ], 200);
    }
}
