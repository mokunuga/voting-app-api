<?php

namespace App\Http\Controllers;

use App\Candidate;
use App\Post;
use App\User;
use App\Vote;
use Dotenv\Validator;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\Array_;

class VoteController extends Controller
{
    public $authController;
    public function _construct(AuthController $authController) {
        $this->authController = AuthController::class;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $votes = Vote::all();
        return response()->json($votes);
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
        $this->authController = new AuthController();
        $currentUser = $this->authController->me()->getData()->id;
        $rules = [
            'candidate_id' => 'required|integer',
        ];
        $input = $request->only(
            'candidate_id');
        $validator = \Illuminate\Support\Facades\Validator::make($input, $rules);
        if($validator->fails()) {
            $error = $validator->messages()->toJson();
            return response()->json(['success'=> false, 'error'=> $error]);
        }
        $vote = Vote::create(['candidate_id' => $request->candidate_id, 'vote_count' => 1, 'user_id' => $currentUser]);
        return response()->json(['success' => true, 'data' => $vote]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $vote = Vote::all()->groupBy('user_id');
        return response()->json($vote);
    }

    public function getVoteCount($candidate_id) {
        $vote_count = Vote::where('candidate_id', $candidate_id)->count();
        return response()->json($vote_count);
    }

    public function getTotalVoteCount() {
        $candidates = Candidate::all();
        $vote_count = [];
        foreach ($candidates as $candidate) {
            $temp = new \stdClass();
            $vote = Vote::where('candidate_id', $candidate->id);
            $temp->candidate = $candidate->first_name . ' ' . $candidate->last_name;
            $temp->post = $candidate->post()->get()[0]->name;
            $temp->vote_count = $vote->count();
            $voters = $vote->get()->all();
            $temp->users = [];
            foreach ($voters as $users) {
                // array_push($temp->users, $users->users_id);
                array_push($temp->users, User::select('name')->where('id', $users->user_id)->get()->first());
            }
            array_push($vote_count, $temp);
        }
        return response()->json($vote_count);
    }

    public function hasUserVoted($candidate_id) {
        $post_id = Candidate::select('post_id')->where('id', $candidate_id)->get()->first()->post_id;
        $users = $this->userPerPost($post_id);
        $this->authController = new AuthController();
        $currentUser = $this->authController->me();
        if(in_array($currentUser->getData()->id, $users)) {
            return response()->json(['success' => true]);
        }else {
            return response()->json(['success' => false]);
        }
    }
    public function userPerPost($post_id) {
        $candidates = Candidate::where('post_id', $post_id)->get()->all();
        $users = [];

        foreach ($candidates as $candidate) {
            $voters = Vote::where('candidate_id', $candidate->id)->get()->all();
            foreach ($voters as $user) {
                array_push($users, $user->user_id);
            }
        }
        return $users;
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Vote  $vote
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
