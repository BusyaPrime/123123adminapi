<?php


namespace App\Domain\TrackingSession\Resources;


use App\Domain\TrackingSession\Models\TrackingSession;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class TrackingSessionResource
 * @package App\Domain\TrackingSession\Resources
 * @mixin TrackingSession
 */
class TrackingSessionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'session_id' => $this->id,
            'session_token' => $this->session_token
        ];
    }
}

?>
