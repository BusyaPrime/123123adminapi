<?php


namespace App\Domain\Articles\Jobs;


use App\Domain\Articles\Models\Article;
use App\Domain\Articles\Models\ArticleTranslation;
use App\Domain\Articles\Requests\ArticleRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreArticleJob
{

    use Dispatchable, Queueable;

    protected $request;

    public function __construct(ArticleRequest $request)
    {
        $this->request = $request;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $article = new Article();
            
            if ($this->request->hasFile('image')) {
                $article->deleteImage();
                $article->image = $article->uploadImage($this->request->file('image'));
            }

            $article->save();

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

            $article->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $article;
    }
}
