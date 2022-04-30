<?php

namespace App\Http\Controllers;

use App\invoices;
use App\invoices_attachments;
use App\invoices_details;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\fileExists;

class InvoicesDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function show(invoices_details $invoices_details)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = invoices::find($id)->first();
        $details = invoices_details::where('id_invoice', '=', $id)->get();
        $attachments = invoices_attachments::where('id_invoice', '=', $id)->get();
        return view('invoices.details', compact('invoice', 'details', 'attachments'));
    }

    public function openFile($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;
        $file = Storage::disk('attachments')->getDriver()->getAdapter()->applyPathPrefix($path);

        return response()->file($file);
    }

    public function downloadFile($invoice_number, $file_name)
    {
        $path = $invoice_number . '/' . $file_name;
        $file = Storage::disk('attachments')->getDriver()->getAdapter()->applyPathPrefix($path);

        return response()->download($file);
    }

    public function deleteFile(Request $request)
    {
        invoices_attachments::destroy($request->id_file);

        $path = $request->invoice_number . '/' . $request->file_name;
        Storage::disk('attachments')->delete($path);
        return redirect()->back()->with(['success' => 'تم حذف المرفق بنجاح.']);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, invoices_details $invoices_details)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices_details  $invoices_details
     * @return \Illuminate\Http\Response
     */
    public function destroy(invoices_details $invoices_details)
    {
        //
    }
}
