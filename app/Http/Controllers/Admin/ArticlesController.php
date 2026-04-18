<?php


namespace App\Http\Controllers\Admin;

use App\Domain\Articles\Filters\ArticleFilter;
use App\Domain\Articles\Jobs\StoreArticleJob;
use App\Domain\Articles\Jobs\UpdateArticleJob;
use App\Domain\Articles\Models\Article;
use App\Domain\Articles\Requests\ArticleRequest;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new ArticleFilter($request);
        $article = Article::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.articles.index', [
            'articles' => $article,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.articles.create');
    }


    public function store(ArticleRequest $request)
    {
        try {
            $article = $this->dispatchSync(new StoreArticleJob($request));
            $data = ['type' => 'article', 'article_id' => $article->id];

            $request->merge(['title' => $article->title ?? 'messages.push_messages.article']);
            $request->merge(['message' => $article->short ?? 'messages.push_messages.article_text']);
            $this->dispatchSync(new SendPushJob($request, 'users', false, $data));

            return redirect()->route('admin.articles.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            report($exception);
            return redirect()->route('admin.articles.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(Article $article)
    {
        return view('admin.articles.edit', [
            'item' => $article
        ]);
    }

    public function update(Article $article, ArticleRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateArticleJob($article, $request));
            return redirect()->route('admin.articles.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.articles.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Article $article)
    {
        try {
            $article->deleteImage();
            $article->delete();
            return redirect()->route('admin.articles.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.articles.index')->with('danger', trans('admin.destroy_failed'));
        }
    }

    public function destroyImage(Article $article)
    {
        try {
            $article->deleteImage();
            $article->save();
            return response()->json('', 204);
        } catch (\Exception $exception) {
            return response()->json($exception, 500);
        }
    }
}
