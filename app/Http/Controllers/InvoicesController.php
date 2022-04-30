<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\invoices;
use App\User;
use App\invoices_attachments;
use App\invoices_details;
use App\Notifications\AddInvoice;
use App\sections;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

use Maatwebsite\Excel\Facades\Excel;

class InvoicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = invoices::all();
        $pageTitle = 'قائمة الفواتير';
        return view('invoices.invoices', compact('invoices', 'pageTitle'));
    }

    public function paid()
    {
        $invoices = invoices::where('value_status', 1)->get();
        $pageTitle = 'الفواتير المدفوعة';
        return view('invoices.invoices', compact('invoices', 'pageTitle'));
    }

    public function unPaid()
    {
        $invoices = invoices::where('value_status', 2)->get();
        $pageTitle = 'الفواتير الغير مدفوعة';
        return view('invoices.invoices', compact('invoices', 'pageTitle'));
    }

    public function paidPartial()
    {
        $invoices = invoices::where('value_status', 3)->get();
        $pageTitle = 'الفواتير المدفوعة جزئيا';
        return view('invoices.invoices', compact('invoices', 'pageTitle'));
    }

    public function archive()
    {
        $invoices = invoices::onlyTrashed()->get();
        $pageTitle = 'الفواتير المؤرشفة';
        return view('invoices.invoices', compact('invoices', 'pageTitle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = sections::all();
        return view('invoices.add', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        invoices::create([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'section_id' => $request->section_id,
            'product' => $request->product,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'rate_vat' => $request->rate_vat,
            'value_vat' => $request->value_vat,
            'total' => $request->total,
            'note' => $request->note,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
        ]);

        $invoice_id = invoices::latest()->first()->id;

        invoices_details::create([
            'id_invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'section' => $request->section_id,
            'product' => $request->product,
            'note' => $request->note,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'user' => Auth::user()->name,
        ]);

        if ($request->hasFile('pic')) {
            $image = $request->file('pic');
            $file_name = time() . '.' . $image->getClientOriginalExtension();

            invoices_attachments::create([
                'file_name' => $file_name,
                'invoice_number' => $request->invoice_number,
                'created_by' => Auth::user()->name,
                'id_invoice' => $invoice_id
            ]);

            $request->pic->move(public_path('Attachments/' . $request->invoice_number), $file_name);
        }

        $users = User::all();
        Notification::send($users, new AddInvoice($invoice_id));

        return redirect()->back()->with('success', 'تم اضافة الفاتورة بنجاح.');
    }

    public function getProduct($id)
    {
        $products = DB::table('products')->where('section_id', $id)->pluck('product_name', 'id');
        return json_encode($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function show(invoices $invoices)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $invoice = invoices::find($id);
        $sections = sections::all();
        return view('invoices.edit', compact('invoice', 'sections'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoice = invoices::find($request->invoice_id);
        $invoice_details = invoices_details::where('id_invoice', $request->invoice_id);

        $invoice->update([
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'section_id' => $request->section_id,
            'product' => $request->product,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'rate_vat' => $request->rate_vat,
            'value_vat' => $request->value_vat,
            'total' => $request->total,
            'note' => $request->note,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
        ]);

        $invoice_details->update([
            'section' => $request->section_id,
            'product' => $request->product,
            'note' => $request->note,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
        ]);

        return redirect()->back()->with('success', 'تم تعديل الفاتورة بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\invoices  $invoices
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoice = invoices::find($id) ? invoices::find($id) :  $invoice = invoices::onlyTrashed($id)->findOrFail($id);

        # Delete

        $attachment = invoices_attachments::where('id_invoice', $id)->first();
        if (!empty($attachment->invoice_number)) {
            Storage::disk('attachments')->deleteDirectory($attachment->invoice_number);
        }

        $invoice->forceDelete();
        return redirect()->back()->with('success_delete', 'تم حذف الفاتورة بنجاح.');
    }


    public function archiving(Request $request)
    {
        $id = $request->invoice_id;
        $invoice = invoices::find($id) ? invoices::find($id) :  $invoice = invoices::onlyTrashed($id)->findOrFail($id);

        # Un Archive [ Restore ]
        if ($invoice->deleted_at) {
            $invoice->restore();
            return redirect()->back()->with('success_restore_archive', 'تم استعادة الفاتورة بنجاح.');
        }

        # Archive [ Soft Delete ]
        $invoice->Delete();
        return redirect()->back()->with('success_archive', 'تم ارشفة الفاتورة بنجاح.');
    }

    public function editPaymentStatus($id)
    {
        $invoices = invoices::findOrFail($id);
        return view('invoices.editPaymentStatus', compact('invoices'));
    }

    public function updatePaymentStatus(Request $request)
    {
        $invoice = invoices::findOrFail($request->invoice_id);
        $status = $request->status == '1' ? 'مدفوعة' : 'مدفوعة جزئيا';

        $invoice->update([
            'value_status' => $request->status,
            'status' => $status,
            'payment_date' => $request->payment_date
        ]);

        invoices_details::create([
            'id_invoice' => $request->invoice_id,
            'invoice_number' => $request->invoice_number,
            'section' => $request->section_id,
            'product' => $request->product,
            'note' => $request->note,
            'status' => $status,
            'value_status' => $request->status,
            'payment_date' => $request->payment_date,
            'user' => Auth::user()->name,
        ]);

        return redirect()->back()->with(['success', 'تم تعديل الفاتورة بنجاح.']);
    }

    public function previewPrint($id)
    {
        $invoice = invoices::findOrFail($id);
        return view('invoices.previewPrint', compact('invoice'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
        return redirect()->back()->with(['success', 'تم تعديل الفاتورة بنجاح.']);
    }
}
