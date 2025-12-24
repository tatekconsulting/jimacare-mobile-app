<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
        	'from'      => $this->from_id,
        	'message'   => $this->message,
	        'sent_at'   => $this->created_at->format('d/m/Y \a\t H:i')
        ];
    }
}
