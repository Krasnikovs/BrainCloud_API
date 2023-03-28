<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class FileResource extends JsonResource
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
            'id'=> $this->id,
            'title'=> $this->title,
            'description'=> $this->description,
            'file'=> URL::signedRoute('file.file', ['file' => $this->id]),
            'size'=> $this->size,
            'type' => $this->type,
            'folder' => $this->folder,
            'user' => $this->user,
            'created_at' => $this->created_at->format('d-m-Y G:i')
        ];
    }
}
