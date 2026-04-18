<?php


namespace App\Domain\Tickets\Models;


use App\Services\FilterService\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class Ticket extends Model
{
    use Filterable;

    protected $guarded = ['id'];

    public function uploadFile(UploadedFile $image)
    {
        $extension = $image->getClientOriginalExtension();
        $filename = $this->id.'_'.uniqid().'.'.$extension;
        $image->move(public_path('uploads/tickets/'), $filename);
        return $filename;
    }
}
