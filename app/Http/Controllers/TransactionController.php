<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Complaint;
use Illuminate\Http\Request;
use App\Services\FatoorahServices;

class TransactionController extends Controller
{
    private $fatoorahservices;
    public function __construct (FatoorahServices $fatoorahservices)
    {
        $this->fatoorahservices=$fatoorahservices;

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $transactions=Transaction::select('*')
    ->when($request->has('paymentId'), function ($query) use ($request) {
        $query->where('paymentId', $request->paymentId)->latest();
    })
    ->when($request->has('transaction_status'), function ($query) use ($request) {
        $query->whereNull('transaction_status', $request->transaction_status)->latest();
    })

    ->paginate(10);

        return response()->json($transactions,200);
    }
    public function paymentCallBack (Request $request){
        /* this function is called when the payment transation is success
         * we pass the paymentId to the getPaymentStatus function this function
         * return the payment info
         * after we fetch the payment info we update the transaction in our database
         * and we also update the order (make it paid) the return success response
         */
         try{
         $data=[];
         $data['key'] = $request->paymentId;
         $data['KeyType']='paymentId';
         $paymentData= $this->fatoorahservices->getPaymentStatus($data);
           $invoice_id =$paymentData['Data']['InvoiceId'];
           $transaction=Transaction::where('invoiceId',$invoice_id)->first();
         //search where invoice_id = $paymentData['Data']['InvoiceId']
             $transaction->update([
             'paymentId'=>$paymentData['Data']['InvoiceTransactions'][0]['PaymentId'],
             'card_number' =>$paymentData['Data']['InvoiceTransactions'][0]['CardNumber'],
             'transaction_status' =>$paymentData['Data']['InvoiceTransactions'][0]['TransactionStatus'],
             ]);
            $complaint_id=$transaction->complaint_id;
            $complaint=Complaint::where('id',$complaint_id)->first();
            $complaint->update([
             'paid'=>1
            ]);
             return  response()->json(['transaction'=>$transaction,'payment_Data'=>$paymentData['Data'],'message'=>'Transaction has been updated successfully'],200);
     }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }}

     public function paymentError (Request $request){
        /* this function is called when the payment transation has an error
         * we pass the paymentId to the getPaymentStatus function this function
         * return the payment info
         * after we fetch the payment info we update the transaction in our database
         * then return the failure response
         */
         try{
         $data=[];
         $data['key'] = $request->paymentId;
         $data['KeyType']='paymentId';
          $paymentData= $this->fatoorahservices->getPaymentStatus($data);
           $invoice_id =$paymentData['Data']['InvoiceId'];
           $transaction=Transaction::where('invoiceId',$invoice_id)->first();
         //search where invoice_id = $paymentData['Data']['InvoiceId']
         // return $paymentData['Data']['InvoiceTransactions'][0]['PaymentId'];
             $transaction->update([
             'paymentId'=>$paymentData['Data']['InvoiceTransactions'][0]['PaymentId'],
             'card_number' =>$paymentData['Data']['InvoiceTransactions'][0]['CardNumber'],
             'transaction_status' =>$paymentData['Data']['InvoiceTransactions'][0]['TransactionStatus'],
             ]);


             return  response()->json(['transaction'=>$transaction,'payment_Data'=>$paymentData['Data'],'message'=>'Transaction did not complete successfully'],200);
     }catch (\Exception $ex) {
         return response()->json(["message"=>$ex->getMessage()], 400);
     }
     }
     public function getTransactionData (){
         $data['key'] = request('paymentId');
         $data['KeyType']='paymentId';
         $paymentData= $this->fatoorahservices->getPaymentStatus($data);
         return  response()->json($paymentData,200);
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
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
