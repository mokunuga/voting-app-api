<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use function MongoDB\BSON\toJSON;

class CandidateResource extends JsonResource
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
            'id'            => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'postIndex' => $this->post_id,
            'post' => $this->post->name,
            'manifesto' => $this->manifesto,
            'candidateImage' => $this->candidate_image
        ];
        // return parent::toArray($request);
    }
}
