<?php


namespace App\Domain\Articles\Jobs;


use App\Domain\Articles\Models\Article;
use App\Domain\Articles\Models\ArticleTranslation;
use App\Domain\Articles\Requests\ArticleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateArticleJob
{

    use Dispatchable, Queueable;

    public $article;

    public $request;

    public function __construct(Article $article, ArticleRequest $request)
    {
        $this->request = $request;
        $this->article = $article;
    }


    /**
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $article = $this->article;

//            $article->category = $this->request->input('category', 'diagnostics');

            if ($this->request->hasFile('image')) {
                $article->deleteImage();
                $article->image = $article->uploadImage($this->request->file('image'));
            }
            $article->save();

            $article->translations()->delete();
            $translations = [];
            
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '' && $translate['short'] == '' && $translate['link'] == ''&& $translate['full'] == '') {
                    continue;
                }
                $translations[] = new ArticleTranslation($translate);
            }
            if (!empty($translations)) {
                $article->translations()->saveMany($translations);
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $article;
    }
}
