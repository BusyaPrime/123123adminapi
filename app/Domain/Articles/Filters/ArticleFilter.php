<?php


namespace App\Domain\Articles\Filters;


use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class ArticleFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'title', 'category', 'created_at',
        'sort', 'perPage'
    ];

    protected $translationTable = 'article_translations';

    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => '-id',
        'title' => '',
    ];

    public function __construct(Request $request)
    {
        $this->input = $this->prepareInput($request->all());
    }

    /**
     * Инициализация фильтра.
     */
    protected function init()
    {
        //Добавляем поля для сортировки
        $this->addSortable('id');
        $this->addSortable('created_at');
        $this->addSortable('title', $this->translationTable);

        $this->addJoin($this->translationTable, function () {
            $this->builder->leftJoin($this->translationTable, function ($join) {
                /**
                 * @var $join \Illuminate\Database\Query\JoinClause
                 */
                $join->on($this->table . '.id', $this->translationTable . '.article_id')->where('locale', \App::getLocale());
            })->select($this->table . '.*');
        });
    }

    /**
     * Поиск по id.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function id($value)
    {
        return $this->builder->where($this->column('id'), $value);
    }

    /**
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function category($value)
    {
        return $this->builder->where($this->column('category'), $value);
    }

    /**
     * Поиск по имени.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function title($value)
    {
        if ($value != '') {
            return $this->builder->whereHas('translations', function ($query) use ($value) {
                /**
                 * @var $query \Illuminate\Database\Eloquent\Builder
                 */
                $query->where('title', 'like', '%' . $value . '%')
                    ->where('locale', \App::getLocale());
            });
        }
    }
}
