<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintAsset;
use App\Models\Employee;
use App\Models\Transaction;
use App\Models\Participant;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use GuzzleHttp\Exception\ClientException;
use App\Services\FatoorahServices;
use App\Traits\PhotoTrait;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VisitNotification;
use App\Notifications\PriceNotification;
use App\Notifications\DeterminePriceNotification;
use App\Notifications\JobStartedNotification;
use App\Notifications\NewComplaintNotification;
use App\Notifications\NewJobNotification;
use App\Notifications\UnAssignNotification;
use Illuminate\Support\Carbon;


class ComplaintController extends Controller
{
    use PhotoTrait;
    private $fatoorahservices;

    public function __construct(FatoorahServices $fatoorahservices)
    {
        $this->fatoorahservices = $fatoorahservices;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $complaints = Complaint::select('*')->with('user:id,first_name,last_name,phone', 'employee:id,first_name,last_name', 'visit', 'visit.employee:id,first_name,last_name', 'transaction')
        
            ->when($request->has('user_id'), function ($query) use ($request) {
                $query->where('user_id', $request->user_id)->latest();
            })
            ->when($request->has('employee_id'), function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id)->latest();
            })
            ->when($request->has('user_type'), function ($query) use ($request) {
                $query->where('user_type', $request->user_type)->latest();
            })
            ->when($request->has('urgent'), function ($query) use ($request) {
                $query->where('urgent', $request->urgent)->latest();
            })
            ->when($request->has('job_finished'), function ($query) {
                $query->where('job_finished', 1)->latest();
            })
            ->when($request->has('service_started'), function ($query) {
                $query->whereNull('service_started')->latest();
            })

            ->when($request->has('payment_method'), function ($query) use ($request) {
                $query->where('payment_method', $request->payment_method)->latest();
            })
            ->when($request->has('paid'), function ($query) use ($request) {
                $query->where('paid', $request->payment_method)->latest();
            })
            ->when($request->has('visit'), function ($query) use ($request) {
                $query->where('needs_visit', 1)->latest();
            })
            ->latest()->paginate(10);
            
        return response()->json($complaints, 200);
    }
    public function userComplaints()
    {
        $user_id = auth('sanctum')->user()->id;
        $complaints = Complaint::where('user_id', $user_id)->with('employee', 'transaction')->latest()->paginate(10);
        return response()->json(["data" => $complaints], 200);
    }

    public function requestRepair(Request $request)
    {
        $user = auth('sanctum')->user();
        $validator = Validator::make(
            $request->all(),
            [
                'user_type' => 'required|max:255',
                'title' => 'required|max:255',
                'description' => 'required|max:255',
                'address_id' => 'required',
                'date' => 'nullable',
                'time' => 'nullable',
                'service_started' => 'nullable',
                'service_ended' => 'nullable',
                'needs_spare' => 'nullable|boolean',
                'determine_price' => 'required|boolean',
                'urgent' => 'nullable|boolean',
                'visit' => 'nullable|boolean',
                'price' => 'nullable',
                'payment_method' => 'nullable',
                // 'paid'=>'nullable',
                'job_finished' => 'nullable',
                'rate' => 'nullable',
                // 'images'=> 'nullable|array|max:3',
                // 'images.*' =>'image|max:512|mimes:jpg,jpeg,bmp,png,webp,svg,heic',
                // 'video'=>'nullable|max:10240'
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $price = $request->price;
            if ($request->user_type == 'tenant') {
                $addressable_type = 'App\Models\Apartment';
                if ($request->needs_spare == 0) {
                    $price = 0;
                }
            } elseif ($request->user_type == 'non_tenant') {
                $addressable_type = 'App\Models\Address';
            }
            $complaint = Complaint::create(
                [
                    'user_id' => $user->id,
                    'user_type' => $request->user_type,
                    // 'employee_id'=>$request->employee_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'date' => $request->date,
                    'time' => Carbon::parse($request->time)->format('H:i:s'),
                    // 'service_started'=>$request->service_started,
                    // 'service_ended'=>$request->service_ended,
                    'needs_spare' => $request->needs_spare ?? 0,
                    'determine_price' => $request->determine_price ?? 0,
                    'urgent' => $request->urgent,
                    'needs_visit' => $request->visit ?? 0,
                    'price' => $price,
                    'payment_method' => $request->payment_method,
                    // 'payment_method'=>$request->payment_method,
                    // 'job_finished'=>$request->job_finished,
                    // 'rate'=>$request->rate,
                    'addressable_id' => $request->address_id,
                    'addressable_type' => $addressable_type
                ]
            );
            if ($request->determine_price == 1 || $request->needs_spare ==1 ) {
                $employees = Employee::whereHas("roles", function ($q) {
                    $q->where("name", "employee");
                })->get();
                // $employees=Employee::all();
                Notification::send($employees, new DeterminePriceNotification($complaint->id));
            } else {
                // $employees=Employee::all();
                $employees = Employee::whereHas("roles", function ($q) {
                    $q->where("name", "employee");
                })->get();
                Notification::send($employees, new NewComplaintNotification());
            }
            if ($request->images) {
                $files = $request->images;
                foreach ($files as $file) {
                    $photo_path = $this->saveBase64File($file, "complaintImage", 'images/assets');

                    /*  $base64_encoded_string=$file;

                $random=  Str::uuid()->toString();
                $file_name="complaintImage".$random.'.'.$extension;
                Storage::put($file_name, $base64_encoded_string);
                return $data = base64_decode(Storage::get($file_name));
                */
                    //      $extension = $file->extension();
                    // if( in_array($extension,['jpg','jpeg','bmp','png','webp','svg'])){
                    //     $type='Image';
                    // }
                    // elseif(in_array($extension,['mkv','mp4','webm'])){
                    //     $type='Video';
                    // }
                    //$ext = pathinfo(storage_path().'/uploads/categories/featured_image.jpg', PATHINFO_EXTENSION);
                    // $photo_path= $this->saveImage($file,"complaintImage",'images/assets');
                    $user->upload()->create([
                        'complaint_id' => $complaint->id,
                        'status' => 'before',
                        'type' => 'Image',
                        'path' => $photo_path
                    ]);
                }
            }
            if ($request->video) {
                $video = $request->video;
                $video_path = $this->saveBase64File($video, "complaintVideo", 'images/assets');
                $user->upload()->create([
                    'complaint_id' => $complaint->id,
                    'status' => 'before',
                    'type' => 'Video',
                    'path' => $video_path
                ]);
            }
            // return $complaint->addressable()->with('building')->get();
            return response()->json(["data" => $complaint], 201);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function checkout(Request $request)
    {
        $user = auth('sanctum')->user();

        $validator = Validator::make(
            $request->all(),
            [
                'complaint_id' => 'required',
                'price' => 'required',

            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            //Fill POST fields array
            $data = [
                //Fill required data
                'NotificationOption' => 'Lnk', //'SMS', 'EML', or 'ALL'
                'InvoiceValue'       => $request->price,
                'CustomerName'       => $user->first_name . " " . $user->last_name,
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
                'ExpiryDate'         => Carbon::now()->add(5, 'day'), //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                //'SourceInfo'         => 'Pure PHP', //For example: (Symfony, CodeIgniter, Zend Framework, Yii, CakePHP, etc)
                //'CustomerAddress'    => $customerAddress,
                //'InvoiceItems'       => $invoiceItems,
            ];

            $invoice_data = $this->fatoorahservices->sendPayment($data);
            $transaction = Transaction::create([
                'complaint_id' => $request->complaint_id,
                'invoiceId' => $invoice_data['Data']['InvoiceId'],
                'invoiceURL' => $invoice_data['Data']['InvoiceURL'],
                'invoice_value' => $request->price,
                'customer_name' => $user->first_name . " " . $user->last_name,
                'customer_phone' => $user->phone,
                'transaction_status' => 'Pending'
            ]);
            //  $admins=Admin::all();
            //  Notification::send($admins, new OrderNotification($order));
            //  Notification::send($service_provider, new OrderNotification($order));

            return  response()->json(['transaction_data' => $transaction, 'invoice_data' => $invoice_data, 'message' => 'order created successfully'], 201);
            // }
        } catch (ClientException $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function requestVisit(Request $request)
    {
        try {
            $complaint = Complaint::find($request->id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'needs_visit' => $request->visit ?? 0,
                    ]
                );
                $user =  User::find($complaint->user_id);
                $user->notify(new VisitNotification($complaint));
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }
            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function determinePrice(Request $request)
    {
        try {
            $complaint = Complaint::find($request->complaint_id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'price' => $request->price ?? 0,
                    ]
                );
                $user =  User::find($complaint->user_id);

                if ($complaint->payment_method == 'MyFatoorah') {

                    $validator = Validator::make(
                        $request->all(),
                        [
                            'complaint_id' => 'required',
                            'price' => 'required',
                        ]
                    );
                    if ($validator->fails()) {
                        return response()->json($validator->errors(), 400);
                    }
                    //Fill POST fields array
                    $data = [
                        //Fill required data
                        'NotificationOption' => 'Lnk', //'SMS', 'EML', or 'ALL'
                        'InvoiceValue'       => $request->price,
                        'CustomerName'       => $user->first_name . " " . $user->last_name,
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
                        'CustomerReference'  => $complaint->id,
                        //'CustomerCivilId'    => 'CivilId',
                        //'UserDefinedField'   => 'This could be string, number, or array',
                        'ExpiryDate'         => Carbon::now()->add(7, 'day'), //The Invoice expires after 3 days by default. Use 'Y-m-d\TH:i:s' format in the 'Asia/Kuwait' time zone.
                        //'SourceInfo'         => 'Pure PHP', //For example: (Symfony, CodeIgniter, Zend Framework, Yii, CakePHP, etc)
                        //'CustomerAddress'    => $customerAddress,
                        //'InvoiceItems'       => $invoiceItems,
                    ];

                    $invoice_data = $this->fatoorahservices->sendPayment($data);
                    $transaction = Transaction::create([
                        'complaint_id' => $complaint->id,
                        'invoiceId' => $invoice_data['Data']['InvoiceId'],
                        'invoiceURL' => $invoice_data['Data']['InvoiceURL'],
                        'invoice_value' => $request->price,
                        'customer_name' => $user->first_name . " " . $user->last_name,
                        'customer_phone' => $user->phone,
                        'transaction_status' => 'Pending'
                    ]);
                    $invoiceURL = $invoice_data['Data']['InvoiceURL'];
                $user->notify(new PriceNotification($request->price , $complaint , $invoiceURL));

                    //  $admins=Admin::all();
                    //  Notification::send($admins, new OrderNotification($order));
                    //  Notification::send($service_provider, new OrderNotification($order));

                    return  response()->json(["data" => $complaint, 'transaction_data' => $transaction, 'invoice_data' => $invoice_data, 'message' => 'order created successfully'], 201);
                    // }

                }
                else{
                    $invoiceURL = null;
                $user->notify(new PriceNotification($request->price , $complaint , $invoiceURL));

                }
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }
            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }


    public function completeComplaintInfo(Request $request)
    {
        try {
            $complaint = Complaint::find($request->complaint_id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'date' => $request->date,
                        'time' => Carbon::parse($request->time)->format('H:i:s'),
                        'urgent' => $request->urgent,
                        'payment_method' => $request->payment_method,
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
                    $transaction = Transaction::create([
                        'complaint_id' => $request->complaint_id,
                        'invoiceId' => $invoice_data['Data']['InvoiceId'],
                        'invoiceURL' => $invoice_data['Data']['InvoiceURL'],
                        'invoice_value' => $complaint->price,
                        'customer_name' => $complaint->user->first_name . " " . $complaint->user->last_name,
                        'customer_phone' => $complaint->user->phone,
                        'transaction_status' => 'Pending'
                    ]);
                    //  $admins=Admin::all();
                    //  Notification::send($admins, new OrderNotification($order));
                    //  Notification::send($service_provider, new OrderNotification($order));

                    return  response()->json(["data" => $complaint, 'transaction_data' => $transaction, 'invoice_data' => $invoice_data, 'message' => 'order created successfully'], 201);
                    // }
                }
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }
            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        $id = request('id');
        //->with('user:id,first_name,last_name,phone','employee:id,first_name,last_name','visit','visit.employee:id,first_name,last_name','transaction')
        $complaint = Complaint::with('assets', 'employee:id,first_name,last_name', 'transaction', 'visit', 'visit.employee:id,first_name,last_name', 'user')->find($id);
        if ($complaint != null) {
            $address = $complaint->addressable()->first();
            if ($complaint->addressable_type == "App\Models\Apartment") {
                $address = $complaint->addressable()->with('building')->first();
            } elseif ($complaint->addressable_type == "App\Models\Address") {
                $address = $complaint->addressable()->first();
            }
            return response()->json(["complaint" => $complaint, "address" => $address], 200);
        } else {
            return response()->json(['error' => 'This id does not exist'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth('sanctum')->user();
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'user_type' => 'required|max:255',
                'title' => 'required|max:255',
                'description' => 'required|max:255',
                'addressable_id' => 'required',
                'date' => 'nullable',
                'time' => 'nullable',
                'service_started' => 'nullable',
                'service_ended' => 'nullable',
                'needs_spare' => 'nullable|boolean',
                'determine_price' => 'nullable|boolean',
                'urgent' => 'nullable|boolean',
                'visit' => 'nullable|boolean',
                'price' => 'nullable',
                'payment_method' => 'nullable',
                'paid' => 'nullable',
                'job_finished' => 'nullable',
                'rate' => 'nullable',
                'canceled' => 'nullable|boolean',
                'images' => 'nullable|array|max:3',
                'images.*' => 'image|max:512|mimes:jpg,jpeg,bmp,png,webp,svg,heic',
                'video' => 'nullable|max:10240'
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $complaint = Complaint::find($request->id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'user_id' => $user->id,
                        'user_type' => $request->user_type,
                        'employee_id' => $request->employee_id,
                        'title' => $request->title,
                        'description' => $request->description,
                        'date' => $request->date,
                        'time' => $request->time,
                        'service_started' => $request->service_started,
                        'service_ended' => $request->service_ended,
                        'needs_spare' => $request->needs_spare ?? 0,
                        'determine_price' => $request->determine_price ?? 0,
                        'urgent' => $request->urgent,
                        'needs_visit' => $request->visit ?? 0,
                        'price' => $request->price,
                        'payment_method' => $request->payment_method,
                        'paid' => $request->paid,
                        'job_finished' => $request->job_finished,
                        'rate' => $request->rate,
                        'canceled' => $request->canceled,
                        'addressable_id' => $request->addressable_id,
                        'addressable_type' => $request->addressable_type
                    ]
                );
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }

            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Complaint $complaint)
    {
        try {
            $complaint_id = request('id');
            $complaint = Complaint::find($complaint_id);
            if ($complaint == null) {
                return response()->json(['message' => 'This Complaint is not exist'], 404);
            }
            $complaint->delete();
            return response()->json([$complaint, 'status' => "success", 'message' => "Complaint deleted successfully"], 200);
        } catch (\Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function UnassignedComplaint(Complaint $complaint)
    {
        //
        try {
            $complaint = Complaint::select("*")
                ->whereNull('employee_id')
                ->get();
            return response()->json($complaint, 200);
        } catch (\Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function EmployeeComplaint(Complaint $complaint)
    {
        try {
            $complaint = Complaint::whereNotNull('employee_id')
                ->whereNotNull('service_started')
                ->latest()
                ->get();
            return response()->json($complaint, 200);
        } catch (\Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function assignComplaint(Complaint $complaint, Request $request)
    {
        //
        $validator = Validator::make(
            $request->all(),
            [
                'employee_id' => 'required',
                'complaint_id' => 'required',

            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $admin = auth('api-employees')->user();
        $employee_id = request('employee_id');
        $complaint_id = request('complaint_id');
        try {
            $complaint = Complaint::with('employee')->find($complaint_id);
            if ($complaint != null) {
                if ($complaint->employee_id == null) {
                    $complaint->update(
                        [
                            'employee_id' => $employee_id,
                        ]
                    );

                    $employee_notify = Employee::find($employee_id);
                    $employee_notify->notify(new NewJobNotification());
                } else {
                    $employee_id = request('employee_id');
                    $complaint_employee = $complaint->employee;
                    $complaint_employee->notify(new UnAssignNotification());
                    $complaint->update(
                        [
                            'employee_id' => $employee_id,
                        ]
                    );

                    $employee_notify = Employee::where('id', $employee_id)->first();
                    $employee_notify->notify(new NewJobNotification());
                }
                $conversation =  $admin->conversations()->whereHas('participants', function ($query) use ($employee_id) {
                    // $admin=auth('api-employees')->user();
                    $query->where('employee_id', $employee_id);
                })->with('participants')->with(['messages' => function ($query) {
                    $query->orderBy('created_at', 'desc')->first();
                }])->first();


                if ($conversation == null) {
                    $conversation = Conversation::create([]);
                    $participants = Participant::create([
                        'conversation_id' => $conversation->id,
                        'employee_id' => $employee_id,
                    ]);
                    $participants = Participant::create([
                        'conversation_id' => $conversation->id,
                        'employee_id' => $admin->id,
                    ]);
                }

                return response()->json(["data" => "Complaint assigned successfully"], 201);
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }
            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function startComplaint(Complaint $complaint)
    {
        $user = auth('api-employees')->user();
        $complaint_id = request('complaint_id');
        try {
            $complaint = Complaint::find($complaint_id);

            if ($user->id != $complaint->employee->id) {
                return response()->json(['error' => 'This complaint is not assigned to you'], 403);
            } else {
                if ($complaint != null) {
                    $complaint->update(
                        [
                            'service_started' => now()
                        ]
                    );
                    $employees = Employee::whereHas("roles", function ($q) {
                        $q->where("name", "employee");
                    })->get();
                    Notification::send($employees, new JobStartedNotification($complaint));
                } else {
                    return response()->json(['error' => ' this id does not exist to modify '], 404);
                }
                return response()->json(["data" => $complaint], 200);
            }
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }
    public function ComplaintAsset(Complaint $complaint)
    {
        $complaint_id = request('complaint_id');
        $complaint = ComplaintAsset::where('complaint_id', $complaint_id)->get();
        if ($complaint != null) {

            return response()->json(["data" => $complaint], 200);
        } else {
            return response()->json(['error' => 'This id does not exist'], 404);
        }
    }
    public function updateComplaint(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
                'rate' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $complaint = Complaint::find($request->id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'rate' => $request->rate,
                    ]
                );
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }

            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }
    public function CanceledComplaint(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'complaint_id' => 'required',
                'canceled' => 'nullable',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $complaint = Complaint::find($request->complaint_id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'canceled' => $request->canceled ?? 0,
                    ]
                );
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }
            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }

    public function paidCash(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        try {
            $complaint = Complaint::find($request->id);
            if ($complaint != null) {
                $complaint->update(
                    [
                        'paid' => 1,
                    ]
                );
            } else {
                return response()->json(['error' => ' this id does not exist to modify '], 404);
            }

            return response()->json(["data" => $complaint], 200);
        } catch (Exception $ex) {
            return response()->json(["message" => $ex->getMessage()], 400);
        }
    }
}
