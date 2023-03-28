<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'image' => $this->getRoleNames()[0] == 'Fake User' && $this->image == 'https://thispersondoesnotexist.com/image' ? $this->image : URL::signedRoute('user.image', ['user' => $this->id, date('his')]),
            'name' => $this->name,
            'email' => $this->email,
            'free_space' => $this->space.' / '.$this->SubscriptionType->max_space.' Gb',
            'subscription_type' => new PlanResource($this->SubscriptionType),
            'role' => $this->getRoleNames(),
        ];
    }
}
