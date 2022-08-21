<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::latest()->get();

        return response()->json([
            "success" => true,
            "message" =>  "Article Success get list",
            "data" => $articles
        ]);
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
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'body' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $user = auth()->user();

        $article = $user->articles()->create([
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return response()->json([
            "success" => true,
            "message" =>  "Article Success submit",
            "data" => $article
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articles = Article::with('comments')->find($id);

        return response()->json([
            "success" => true,
            "message" =>  "Article Success get list",
            "data" => $articles
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(request()->all(), [
            'title' => 'required',
            'body' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }


        // $article = Article::where('id', $id)->update([
        //     'title' => $request->title,
        //     'body' => $request->body,
        // ]);
        $user = auth()->user();
        $article = Article::find($id);
        //validasi yg punya
        if ($user->id != $article->user_id) {
            return response()->json([
                "success" => false,
                "message" =>  "You not own this article",
            ], 403);
        }

        $article->title = $request->title;
        $article->body = $request->body;
        $article->save();

        return response()->json([
            "success" => true,
            "message" =>  "Article Success updated",
            "data" => $article
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $article = Article::find($id);
        //validasi yg punya
        if ($user->id != $article->user_id) {
            return response()->json([
                "success" => false,
                "message" =>  "You not own this article",
            ], 403);
        }
        $article->delete();

        return response()->json([
            "success" => true,
            "message" =>  "Article Success deleted",
            "data" => $article
        ]);
    }
}
