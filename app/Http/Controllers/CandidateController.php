<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\Http\Resources\CandidateResource;
use App\Http\Resources\CandidatesResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return new CandidatesResource(Candidate::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $rules = [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'post_id' => 'required|integer',
            'manifesto' => 'required',
            'candidate_image' => 'required',
        ];
        $input = $request->only(
            'first_name', 'last_name', 'post_id', 'manifesto', 'candidate_image'
        );
        $validator = Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> $error]);
        }
        $candidate = Candidate::create($request->all());
        return response()->json(['success' => true, 'data' => $candidate]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function show(Candidate $candidate)
    {
        return array(new CandidateResource($candidate));
    }

    public function candidateByPost($post_id)
    {
        $candidates = Candidate::where('post_id', $post_id)->get();
        return new CandidatesResource($candidates);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function edit(Candidate $candidate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Candidate $candidate)
    {
        //
        $candidate->update($request->all());
        return response()->json(['status' => 'Successfully updated candidate', 'data'=>$candidate]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Candidate  $candidate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Candidate $candidate)
    {
        //
        $temp = $candidate;
        $candidate->delete();
        return response()->json(['status' => 'Successfully deleted candiate with name ' . $temp->first_name . ' ' . $temp->last_name]);
    }


}
