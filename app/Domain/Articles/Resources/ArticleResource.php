<?php

namespace App\Domain\Articles\Resources;

use Illuminate\Http\Resources\Json\JsonResource;


class ArticleResource extends JsonResource
{
    public function toArray($request)
    {
        if($this->title == null && $this->short == null && $this->full == null){
            $attrs = parent::getTranslation(\App::getLocale());
            $this->title = $attrs->title;
            $this->short = $attrs->short;
            $this->full = $attrs->full;
        }
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short' => $this->short,
            'full' => $this->full,
            'link' => $this->link,
            'image' => $this->imageUrl(),
            'date' => optional($this->created_at)->format('d.m.Y'),
        ];
    }
}
