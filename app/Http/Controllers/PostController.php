<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostAttachment;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;

class PostController extends Controller
{
    use ApiResponseTrait; 

    /**
     * Store a newly created post in storage along with its attachments.
     *
     * @param StorePostRequest $request The validated request data.
     *
     * @return JsonResponse
     *
     * @throws \Exception If there is an error during creation or file storage.
     */
    public function store(StorePostRequest $request): JsonResponse
    {
        $data = $request->validated();

        DB::beginTransaction();
        $allFilePaths = [];

        try {
            
            $post = Post::create($data);

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            $allFilePaths = $this->createNewPostAttachment($files, $post->id);

            DB::commit();

            return $this->apiResponse('Post Created Successfully', 201,new PostResource($post));

        } catch (\Exception $e) { 
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update the specified post and its attachments.
     *
     * @param UpdatePostRequest $request The validated request data.
     * @param Post $post The post to update.
     *
     * @return JsonResponse
     *
     * @throws \Exception If there is an error during the update process.
     */
    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        DB::beginTransaction();
        $allFilePaths = [];

        try {
            $data = $request->validated();
            $post->update($data);

            $deleted_ids = $data['deleted_file_ids'] ?? []; 
            $this->deleteOldPostAttachments($deleted_ids, $post->id);

            /** @var \Illuminate\Http\UploadedFile[] $files */
            $files = $data['attachments'] ?? [];
            $allFilePaths = $this->createNewPostAttachment($files, $post->id);

            DB::commit();

            return $this->apiResponse('Post Updated Successfully', 201,new PostResource($post));
        } catch (\Exception $e) {
            foreach ($allFilePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Remove the specified post from storage if the user is authorized.
     *
     * @param Post $post The post to delete.
     *
     * @return JsonResponse|RedirectResponse
     */
    public function destroy(Post $post): JsonResponse|RedirectResponse
    {
        $id = Auth::id();

        if ($post->user_id !== $id) {
            return $this->apiResponse("You did't have permission to delete this post", 404);
        }

        $post->delete();

        return back();
    }

    /**
     * Download a specific post attachment file.
     *
     * @param PostAttachment $attachment The attachment to download.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download(PostAttachment $attachment)
    {
        return response()->download(Storage::disk('public')->path($attachment->path), $attachment->name);
    }

    /**
     * Toggle like reaction for the specified post.
     *
     * @param Request $request The request containing the reaction type.
     * @param Post $post The post being reacted to.
     *
     * @return JsonResponse
     */
    public function postReaction(Request $request, Post $post): JsonResponse
    {
        $data = $request->validate([
            'reaction' => ['required', Rule::in(['like'])],
        ]);

        $userId = Auth::id();
        $reaction = Reaction::where('user_id', $userId)
            ->where('object_id', $post->id)
            ->where('object_type', Post::class)
            ->first();

        if ($reaction) {
            $hasReaction = false;
            $reaction->delete();
        } else {
            $hasReaction = true;
            Reaction::create([
                'object_id' => $post->id,
                'object_type' => Post::class,
                'user_id' => $userId,
                'type' => $data['reaction'],
            ]);
        }

        $reactions = Reaction::where('object_id', $post->id)->where('object_type', Post::class)->count();

        return $this->apiResponse('Post Updated Successfully', 201,[
            'num_of_reactions' => $reactions,
            'current_user_has_reaction' => $hasReaction,
        ]);
    }

    /**
     * Store new attachment files for a specific post.
     *
     * @param \Illuminate\Http\UploadedFile[] $files Array of uploaded files.
     * @param int $postID The ID of the post to attach files to.
     *
     * @return array
     */
    private function createNewPostAttachment($files, $postID): array
    {
        foreach ($files as $file) {
            $path = $file->store('attachments/'. $postID, 'public');
            $allFilePaths[] = $path;

            PostAttachment::create([
                'post_id' =>  $postID,
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'created_by' => Auth::id(),
            ]);
        }

        return $allFilePaths;
    }

    /**
     * Delete attachments from a post based on a list of IDs.
     *
     * @param array $deleted_ids IDs of attachments to delete.
     * @param int $postID The ID of the related post.
     *
     * @return void
     */
    private function deleteOldPostAttachments($deleted_ids, $postID): void
    {
        $attachments = PostAttachment::query()
            ->whereIn('id', $deleted_ids)
            ->where('post_id', $postID)
            ->get();

        foreach ($attachments as $attachment) {
            $attachment->delete();
        }
    }
}
