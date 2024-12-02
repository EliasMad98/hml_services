<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomNotification;
use Kutia\Larafirebase\Messages\FirebaseMessage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Exports\TableExport;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use App\Notifications\UpdatePriceNotification;
use App\Services\FatoorahServices;
use App\Models\Transaction;
use Illuminate\Support\Carbon;


class DashboardController extends Controller
{
    private $fatoorahservices;

    public function __construct(FatoorahServices $fatoorahservices)
    {
        $this->fatoorahservices = $fatoorahservices;
    }

    public function dataCounts()
    {
        $jobDone = Complaint::yearToDate()->where('job_finished', 1)->count();
        $pendingJobs = Complaint::yearToDate()->whereNotNull('service_started')->where('job_finished', 0)->count();
        $to_do = Complaint::yearToDate()->whereNull('service_started')->where('job_finished', 0)->count();
        return response()->json(['jobDone' => $jobDone, 'pendingJobs' => $pendingJobs, 'to_do' => $to_do], 200);
    }


    public function dataCharts()
    {
        /*
        Complaint::ofToday(); // query Complaints created today
        Complaint::ofLastWeek(); // query Complaints created during the last week
        Complaint::ofLastMonth(); // query Complaints created during the last month
        Complaint::ofLastQuarter(); // query Complaints created during the last quarter
        Complaint::ofLastYear(); // query Complaints created during the last year
        */
        //date_scope=> Today || last_month || last_quarter || last_year || last_week

        $dateScope = request('date_scope');

        if ($dateScope == 'custom') {
            $from_date = request('from_date');
            $to_date = request('to_date');

            $todoJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('job_finished', 0)->whereNull('service_started')->count();
            $jobDone = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('job_finished', 1)->count();
            $pendingJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->whereNotNull('service_started')->where('job_finished', 0)->count();
            $non_tenant_todoJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'non_tenant')->where('job_finished', 0)->whereNull('service_started')->count();
            $non_tenant_jobDone = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'non_tenant')->where('job_finished', 1)->count();
            $non_tenant_pendingJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'non_tenant')->whereNotNull('service_started')->where('job_finished', 0)->count();
            $tenant_todoJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'tenant')->where('job_finished', 0)->whereNull('service_started')->count();
            $tenant_jobDone = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'tenant')->where('job_finished', 1)->count();
            $tenant_pendingJobs = Complaint::whereBetween('created_at', [$from_date, $to_date])->where('user_type', 'tenant')->whereNotNull('service_started')->where('job_finished', 0)->count();
        } else {
            if ($dateScope == 'Today') {
                $sort = 'ofToday';
            } elseif ($dateScope == 'last_week') {
                $sort = 'ofLast7Days';
            } elseif ($dateScope == 'last_month') {
                $sort = 'ofLast30Days';
            } elseif ($dateScope == 'last_quarter') {
                $sort = 'quarterToDate';
            } elseif ($dateScope == 'last_year') {
                $sort = 'yearToDate';
            } else {
                $sort = 'ofLast30Days';
            }
            $todoJobs = Complaint::$sort()->where('job_finished', 0)->whereNull('service_started')->count();
            $jobDone = Complaint::$sort()->where('job_finished', 1)->count();
            $pendingJobs = Complaint::$sort()->whereNotNull('service_started')->where('job_finished', 0)->count();
            $non_tenant_todoJobs = Complaint::$sort()->where('user_type', 'non_tenant')->where('job_finished', 0)->whereNull('service_started')->count();
            $non_tenant_jobDone = Complaint::$sort()->where('user_type', 'non_tenant')->where('job_finished', 1)->count();
            $non_tenant_pendingJobs = Complaint::$sort()->where('user_type', 'non_tenant')->whereNotNull('service_started')->where('job_finished', 0)->count();
            $tenant_todoJobs = Complaint::$sort()->where('user_type', 'tenant')->where('job_finished', 0)->whereNull('service_started')->count();
            $tenant_jobDone = Complaint::$sort()->where('user_type', 'tenant')->where('job_finished', 1)->count();
            $tenant_pendingJobs = Complaint::$sort()->where('user_type', 'tenant')->whereNotNull('service_started')->where('job_finished', 0)->count();
        }

        $all = [
            'todoJobs' => $todoJobs,
            'pendingJobs' => $pendingJobs,
            'jobDone' => $jobDone,
            'count_all' => $todoJobs + $pendingJobs + $jobDone
        ];
        $non_tenant = [
            'non_tenant_todoJobs' => $non_tenant_todoJobs,
            'non_tenant_pendingJobs' => $non_tenant_pendingJobs,
            'non_tenant_jobDone' => $non_tenant_jobDone,
            'count_all' => $non_tenant_todoJobs + $non_tenant_jobDone + $non_tenant_pendingJobs
        ];
        $tenant = [
            'tenant_todoJobs' => $tenant_todoJobs,
            'tenant_pendingJobs' => $tenant_pendingJobs,
            'tenant_jobDone' => $tenant_jobDone,
            'count_all' => $tenant_todoJobs + $tenant_pendingJobs + $tenant_jobDone
        ];

        return response()->json(['all' => $all, 'non_tenant' => $non_tenant, 'tenant' => $tenant], 200);
    }

    public function sendCustomNotification(Request $request)
    {
        $type = $request->type;
        $title = $request->title;
        $body = $request->body;
        $image = $request->image;
        $icon = $request->icon;
        $sound = $request->sound;
        $clickAction = $request->clickAction;
        $priority = $request->priority;


        if ($type == 'non_tenant') {
            $users = User::whereHas('non_tenant')
                ->whereNotNull('fcm_token')
                ->get();
        } elseif ($type == 'tenant') {
            $users = User::whereHas('tenant')
                ->whereNotNull('fcm_token')
                ->get();
        } elseif ($type == 'all') {
            $users = User::whereNotNull('fcm_token')
                ->get();
        } elseif ($type == 'one_user') {
            $user_id = $request->user_id;
            $users = User::where('id', $user_id)
                ->whereNotNull('fcm_token')
                ->get();
        } elseif ($type == 'most_ordered') {

            $users = User::withCount('complaints')
                ->whereNotNull('fcm_token')
                ->orderBy('complaints_count', 'desc')
                ->limit(10);
        } elseif ($type == 'building') {
            $building_id = $request->building_id;

            $users = Tenant::whereHas('apartments', function ($query) use ($building_id) {
                $query->whereHas('building', function ($query) use ($building_id) {
                    $query->where('building_id', $building_id);
                });
            })->get();
        } elseif ($type == 'only_user') {
            $user_id = $request->user_id;
            $users = User::where('id', $user_id)->first();
        }
        // return $users;
        Notification::send($users, new CustomNotification(
            $title,
            $body,
            $image,
            $icon,
            $clickAction,
            $sound,
            $priority
        ));
    }
    public function sendFcmCustomNotification(Request $request)
    {
        $title = $request->title;
        $body = $request->body;
        $image = $request->image;
        $icon = $request->icon;
        $sound = $request->sound;
        $clickAction = $request->clickAction;
        $priority = $request->priority;
        $FcmToken = $request->fcm_token;

        return (new FirebaseMessage)
            ->withTitle($title)
            ->withBody($body)
            ->withImage($image)
            ->withIcon($icon)
            ->withSound($sound)
            ->withClickAction($clickAction)
            ->withPriority($priority)
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            ->asNotification([$FcmToken]); // OR ->asMessage($deviceTokens);

    }

    public function downloadTable()
    {
        return Excel::download(new TableExport, 'table.xlsx');
    }

    public function updateComplaintPrice(Request $request)
    {
        // $user=auth('sanctum')->user();
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'price' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $complaint = Complaint::find($request->id);
            if ($complaint != null) {

                if ($complaint->paid == null) {
                    $complaint->update(
                        [
                            'price' => $request->price,
                        ]
                    );
                    if ($complaint->payment_method == 'MyFatoorah') {
                        //Fill POST fields array
                        $data = [
                            //Fill required data
                            'NotificationOption' => 'Lnk', //'SMS', 'EML', or 'ALL'
                            'InvoiceValue'       => $complaint->price,
                            'CustomerName'       => $complaint->user->first_name . " " . $complaint->user->last_name,
                            //Fill optional data
                            'DisplayCurrencyIso' => 'AED',
                            //'MobileCountryCode'  => '+965',
                            //'CustomerMobile'     => auth('sanctum')->user()->phone,
                            // 'CustomerMobile'     => '0965656565',
                            // 'CustomerEmail'      => 'email@example.com',
                            'CallBackUrl'        => config('app.payment_success_url'),
                            'ErrorUrl'           => config('app.payment_error_url'),
                            // 'ErrorUrl'           => env('payment_error_url'), //or 'https://example.com/error.php'
                            'Language'           => 'en', //or 'ar'
                            'CustomerReference'  => $request->complaint_id,
                            //'CustomerCivilId'    => 'CivilId',
                            //'UserDefinedField'   => 'This could be string, number, or array',
                            'ExpiryDate'         => Carbon::now()->add(7, 'day'), //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                            //'SourceInfo'         => 'Pure PHP', //For example: (Symfony, CodeIgniter, Zend Framework, Yii, CakePHP, etc)
                            //'CustomerAddress'    => $customerAddress,
                            //'InvoiceItems'       => $invoiceItems,
                        ];

                        $invoice_data = $this->fatoorahservices->sendPayment($data);
                        $transaction = Transaction::where('complaint_id' , $request->id)->first();
                        $transaction->update([
                            // 'complaint_id' => $request->complaint_id,
                            'invoiceId' => $invoice_data['Data']['InvoiceId'],
                            'invoiceURL' => $invoice_data['Data']['InvoiceURL'],
                            'invoice_value' => $complaint->price,
                            'customer_name' => $complaint->user->first_name . " " . $complaint->user->last_name,
                            'customer_phone' => $complaint->user->phone,
                            'transaction_status' => 'Pending'
                        ]);
                    // if ($complaint->user_id != null) {
                        $user =  User::find($complaint->user_id);
                        $invoiceURL = $invoice_data['Data']['InvoiceURL'];
                        $user->notify(new UpdatePriceNotification($request->price ,$complaint , $invoiceURL));
                    }
                        $user = User::find($complaint->user_id);
                        // $user->notify(new UpdatePriceNotification());
                    $user->notify(new UpdatePriceNotification($request->price ,$complaint ));

                    // }
                    return response()->json(["data" => $complaint], 200);

                } else {
                    return response()->json(['error' => ' you cant update this Complaint because its paid'], 404);
                }
            } else {
                return response()->json(['error' => 'this id does not exist to modify '], 404);
            }
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function getIncomeData(Request $request)
    {
        $year = request('year');
        if ($year == null) {

            $year = date('Y');
        }
        $totaleachmonth = DB::table('complaints')
            ->whereYear('created_at', $year)
            ->where('paid', 1)
            ->when($request->has('user_type'), function ($query) use ($request) {
                $query->where('user_type', $request->user_type);
            })
            ->when($request->has('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method);
            })
            ->select(DB::raw('SUM(price) as total_amount, MONTH(created_at) as month'))
            ->groupBy(DB::raw('MONTH(created_at)', 'asc'))->get();

        //  $total_revenues=collect($totaleachmont)->toArray();

        // return response()->json($totaleachmont,200);

        $total_revenues = collect($totaleachmonth)->toArray();
        $values = [];


        foreach ($total_revenues as $total_revenue) {
            $i = $total_revenue->month;
            $values[$i] = $total_revenue->total_amount;
            $i++;
        }
        // return $values;
        $valuess = [];

        for ($i = 1; $i <= 12; $i++) {

            if (array_key_exists($i, $values)) {

                $values[$i] = $values[$i];
            } else {
                $values[$i] = 0;
            }
            array_push($valuess, [$i => $values[$i]]);
        }
        return response()->json($valuess, 200);
    }
}
