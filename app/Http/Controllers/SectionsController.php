<?php

namespace App\Http\Controllers;

use App\sections;
use Auth;
use App\Http\Requests\StoreSection;
use App\Http\Requests\UpdateSection;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = sections::all();

        return view('sections.sections', ['sections' => $sections]);
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
    public function store(StoreSection $request)
    {
        sections::create([
            'section_name' => $request->section_name,
            'description' => $request->description,
            'Created_by' => (Auth::user()->name),
        ]);

        return redirect()->back()->with(['Add' => 'تم اضافة القسم بنجاح.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function show(sections $sections)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function edit(sections $sections)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSection $request)
    {
        $sections = sections::find($request->id);

        $sections->update([
            'section_name' => $request->section_name,
            'description' => $request->description
        ]);

        return redirect()->back()->with(['Edit' => 'تم تعديل القسم بنجاح.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\sections  $sections
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        sections::destroy($request->id);

        return redirect()->back()->with(['Delete' => "تم حذف قسم  {$request->section_name}  بنجاح."]);
    }
}
