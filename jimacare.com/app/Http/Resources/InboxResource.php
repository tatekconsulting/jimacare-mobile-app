<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InboxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $u = (auth()->id() == $this->seller_id) ? $this->client : $this->seller;

    	return [
        	'user'      => [
        		'id'        => $u->id,
		        'name'      => $u->name,
		        'profile'   => asset($u->profile ?? 'img/undraw_profile.svg')
	        ],
	        'messages'  => MessageResource::collection($this->messages),
        ];
    }
}
