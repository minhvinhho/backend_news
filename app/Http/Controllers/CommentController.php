<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Mail\CommentConfirmation;
use App\Mail\NotifyAdmin;
use App\Mail\NotifyCommentThread;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Config;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class CommentController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole('author')) {
            $authorsArticleIDs = Article::where('user_id', Auth::user()->id)->pluck('id');
            $comments = Comment::whereIn('article_id', $authorsArticleIDs)
                ->with('article', 'user', 'replies')
                ->latest()
                ->noReplies()
                ->paginate(config('blog.item_per_page'));
        } else {
            $comments = Comment::with('article', 'user', 'replies')
                ->latest()
                ->noReplies()
                ->paginate(config('blog.item_per_page'));
        }
        return view('backend.commentList', compact('comments'));
    }

    public function update(Request $request, $commentId)
    {
        $comment = Comment::find($commentId);
        try {
            $comment->update(
                [
                    'content' => $request->get('content'),
                    'countEdit' => $comment->countEdit + 1,
                ]
            );
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('errorMsg', $this->getMessage($e));
        }
        return redirect()->route('comments')->with('successMsg', 'Comment updated');
    }

    public function togglePublish($commentId)
    {
        $comment = Comment::find($commentId);
        try {
            $comment->update(
                [
                    'is_published' => !$comment->is_published,
                    'published_at' => new \DateTime(),
                ]
            );
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('errorMsg', $this->getMessage($e));
        }
        return redirect()->route('comments')->with('successMsg', 'Comment updated');
    }

    public function destroy($commentId)
    {
        try {
            $comment = Comment::find($commentId);
            Article::where('id', $comment->article_id)->decrement('comment_count');
            Comment::destroy($commentId);
        } catch (\PDOException $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->back()->with('errorMsg', $this->getMessage($e));
        }
        return redirect()->route('comments')->with('successMsg', 'Comment deleted');
    }

    public function confirmComment(Request $request, $commentId)
    {
        try {
            $this->validate($request, ['token' => 'required']);

            $comment = Comment::where('id', $commentId)
                ->where('token', $request->get('token'))
                ->with('article')
                ->first();

            if (is_null($comment)) {
                return redirect()->route('home')->with('errorMsg', 'Invalid request');
            }

            if ($comment->is_published) {
                return redirect()->route('get-article', [$comment->article->id])
                    ->with('warningMsg', 'Comment already published');
            }

            $comment->update(['is_published' => 1, 'is_confirmed' => 1, 'published_at' => now()]);
            if ($comment->user->isReader()) {
                $comment->user->reader->update(['is_verified' => 1]);
            }
            //notify all user of the comment thread about the new comment except him person who replied
            if ($comment->parent_comment_id) {
                $threadUserIDs = Comment::where('parent_comment_id', $comment->parent_comment_id)
                    ->orWhere('id', $comment->parent_comment_id)
                    ->pluck('user_id');

                $threadUserEmails = User::whereIn('id', $threadUserIDs)
                    ->where('email', '!=', $comment->user->email)
                    ->pluck('email')
                    ->unique()
                    ->toArray();

                Mail::to($threadUserEmails)->queue(new NotifyCommentThread($comment));
            }
        } catch (\Exception $e) {
            Log::error($this->getLogMsg($e));
            return redirect()->route('get-article', [$comment->article->id])
                ->with('errorMsg', $this->getMessage($e));
        }
        return redirect()->route('get-article', [$comment->article->id])
            ->with('successMsg', 'Comment confirmed successfully!');
    }
}
