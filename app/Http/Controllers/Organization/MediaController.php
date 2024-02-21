<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organization;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMedias(Organization $organization){
        $medias=$organization->media->toArray();
        return response()->json($medias);
    }
    public function index()
    {
        //
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
        // return response()->json(['message' => $request]);
        $originMedia = $request->content;
        $media = $request->file($originMedia);
        if ($media) {
            foreach ($media as $m) {
                $medianame = $m->getClientOriginalName();
                $getId = auth()->user()->id;
                $path = $m->storeAs((string)$getId . '/uploads', $medianame, 'media');

                // Store media into the database
                // ...

                // Samples
                // $m->getSize();
                // $m->getMimeType();
                // $m->getClientOriginalExtension();
                // ...
            }

            return response()->json(['message' => 'Media uploaded successfully', 'path' => $path]);
        } else {
            return response()->json(['message' => 'No media were uploaded'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
