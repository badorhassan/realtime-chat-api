<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Message;
use App\Events\MessageSent;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Real-Time Chat API",
 *     version="1.0.0",
 *     description="API for a real-time chat system"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

class ChatController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1'); // Rate limit: 60 requests per minute
    }
/**
     * @OA\Post(
     *     path="/realtime-chat-api/public/api/chat/send",
     *     summary="Send a message",
     *     tags={"Chat"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="receiver_id", type="integer", example=2),
     *             @OA\Property(property="message", type="string", example="Hello, how are you?")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="sender_id", type="integer", example=1),
     *             @OA\Property(property="receiver_id", type="integer", example=2),
     *             @OA\Property(property="message", type="string", example="Hello, how are you?"),
     *             @OA\Property(property="status", type="string", example="delivered"),
     *             @OA\Property(property="created_at", type="string", example="2024-03-26T12:00:00Z")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=400, description="Validation error")
     * )
     */
    public function sendMessage(Request $request)
    {
        // Determine input source based on request method
        $receiverId = $request->input('receiver_id') ?? $request->receiver_id;
        $message = $request->input('message') ?? $request->message;
    
        // Validate inputs
        $validator = Validator::make([
            'receiver_id' => $receiverId,
            'message' => $message
        ], [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }
    
        try {
            $message = $this->messageService->sendMessage(
                auth()->id(),
                $receiverId,
                $message
            );
    
            return response()->json($message, 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Message sending failed',
                'details' => $e->getMessage()
            ], 500);
        }
    }
/**
     * @OA\Get(
     *     path="/realtime-chat-api/public/api/chat/messages/{user_id}",
     *     summary="Retrieve paginated messages for a user",
     *     tags={"Chat"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of messages",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="sender_id", type="integer", example=1),
     *                 @OA\Property(property="receiver_id", type="integer", example=2),
     *                 @OA\Property(property="message", type="string", example="Hello, how are you?"),
     *                 @OA\Property(property="status", type="string", example="Sent"),
     *                 @OA\Property(property="created_at", type="string", example="2024-03-26T12:00:00Z")
     *             )),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="total", type="integer", example=20)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function getMessages($userId, Request $request)
    {
        $messages = $this->messageService->getMessages($userId, $request->query('page', 1));
        return response()->json([
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'total' => $messages->total(),
            ],
        ]);
    }
/**
     * @OA\Patch(
     *     path="/realtime-chat-api/public/api/chat/read/{message_id}",
     *     summary="Mark a message as read",
     *     tags={"Chat"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="message_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Message marked as read"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Message not found")
     * )
     */
    public function markAsRead($messageId)
    {
        $messageId = $this->messageService->markMessageAsRead($messageId);

        //return response()->json(null, 200);
        
        return response()->json(['status' => 'success']);
    }
    
}