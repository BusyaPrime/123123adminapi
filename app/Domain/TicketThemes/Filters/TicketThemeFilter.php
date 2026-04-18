<?php

namespace App\Domain\TicketThemes\Filters;

use App\Services\FilterService\Filter;
use Illuminate\Http\Request;

class TicketThemeFilter extends Filter
{

    /**
     * Доступные фильтры.
     *
     * @var array
     */
    protected $available = [
        'id', 'title', 'priority',
        'sort', 'perPage'
    ];

    protected $translationTable = 'ticket_theme_translations';

    /**
     * Фильтры по умолчанию.
     *
     * @var array
     */
    protected $defaults = [
        'sort' => 'priority'
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
        $this->addSortable('priority');
        $this->addSortable('title', $this->translationTable);

        $this->addJoin($this->translationTable, function () {
            $this->builder->leftJoin($this->translationTable, function ($join) {
                /**
                 * @var $join \Illuminate\Database\Query\JoinClause
                 */
                $join->on($this->table . '.id', $this->translationTable . '.ticket_theme_id')->where('locale', \App::getLocale());
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
     * Поиск по имени.
     *
     * @param $value
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function title($value)
    {
        return $this->builder->whereHas('translations', function ($query) use ($value) {
            /**
             * @var $query \Illuminate\Database\Eloquent\Builder
             */
            $query->where('title', 'like', '%' . $value . '%')
                ->where('locale', \App::getLocale());
        });
    }
}
