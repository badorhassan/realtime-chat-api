<?php
/**
 * @OA\Post(
 *     path="/api/chat/send",
 *     summary="Send a chat message",
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="receiver_id", type="integer", example=2),
 *             @OA\Property(property="message", type="string", example="Hello!")
 *         )
 *     ),
 *     @OA\Response(response=201, description="Message sent")
 * )
 */
namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth:api');
        $this->middleware('throttle:60,1'); // Rate limit: 60 requests per minute
    }

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

    public function getMessages($userId, Request $request)
    {
        $messages = $this->messageService->getMessages($userId, $request->query('page', 1));
        return response()->json($messages);
    }

    public function markAsRead($messageId)
    {
        $message = $this->messageService->markMessageAsRead($messageId);
        return response()->json(['status' => 'success']);
    }
}