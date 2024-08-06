<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CreateCommentRequest;
use App\Http\Requests\Comment\ListCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;
use App\Traits\ApiResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    use ApiResponse, AuthorizesRequests;

    public function __construct(private CommentRepositoryInterface $commentRepository)
    {
    }

    /**
     * @param ListCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ListCommentRequest $request)
    {
        try {
            $comments = $this->commentRepository->list($request->validated());
            return CommentResource::collection($comments)->response();
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_list_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_list_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param CreateCommentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateCommentRequest $request)
    {
        try {
            $comment = $this->commentRepository->create($request->validated());
            return $this->response(
                __('messages.resource.success.resource_create_success'),
                Response::HTTP_CREATED,
                $comment->toArray()
            );
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_create_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_create_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $id
     * @return CommentResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try {
            $comment = $this->commentRepository->show($id);
            return new CommentResource($comment);
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_show_fail') . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_show_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param UpdateCommentRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommentRequest $request, string $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Authorize the user to update his own comment and not other
            $this->authorize('update', $comment);
            $comment = $this->commentRepository->update($id, $request->validated());
            return $this->response(
                __('messages.resource.success.resource_update_success'),
                Response::HTTP_OK,
                $comment->toArray()
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error(__('messages.resource.un_authorized') . ' :' . $e->getMessage());
            return response()->json(['error' => __('messages.resource.un_authorized')], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_update_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_update_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Authorize the user to delete his own comment and not other
            $this->authorize('update', $comment);
            $this->commentRepository->delete($id);
            return $this->response(
                __('messages.resource.success.resource_delete_success'),
            );
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            Log::error(__('messages.resource.un_authorized') . ' :' . $e->getMessage());
            return response()->json(['error' => __('messages.resource.un_authorized')], Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            Log::error(__('messages.resource.fail.resource_update_fail') . ' :' . $e->getMessage());
            return response()->json(['message' => __('messages.resource.fail.resource_delete_fail')], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
