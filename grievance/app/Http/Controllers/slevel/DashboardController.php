<?php

namespace App\Http\Controllers\slevel;

use App\Events\ComplaintResolved as AppComplaintResolved;
use App\Events\InquiryResolvedEvent;
use App\Http\Controllers\Controller;
use App\Jobs\ComplaintResolved;
use App\Jobs\InquiryResolved;
use App\Jobs\RatingMail;
use App\Models\ActionTriggers;
use App\Models\Complaint;
use App\Models\ComplaintEvaluation;
use App\Models\Department;
use App\Models\Inquiry;
use App\Models\InquiryEvaluation;
use App\Models\InquiryResolution;
use App\Models\InquiryTransactions;
use App\Models\Notification;
use App\Models\Resolution;
use App\Models\Transition;
use App\Traits\SmsTrait;
use App\User;
use Symfony\Component\HttpFoundation\Request;
use App\Models\InquiryType;
use App\Exports\OtherInquiryExport;
use Carbon\Carbon;
use App\Exports\OtherExport;
use App\Models\Product;
use App\Models\Category;
use App\Models\ComplaintSource;
use Maatwebsite\Excel\Facades\Excel;
class DashboardController extends Controller
{
    use SmsTrait;
    public function index(Request $req)
    {
        try {
          $complaintid = Complaint::pluck('id')->all();
           //// $resolveid = Resolution::pluck('complaint_id')->all();
          $resolveid = Resolution::where('user_id',auth()->user()->id)->pluck('complaint_id')->all();
            $complaintsource = ComplaintSource::all();
            $category = Category::all();
            $product = Product::all();
            $countactive = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->get()->unique('complaintid')->count();
            $countresolved = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 1])->whereIn('complaintid',$complaintid)->whereIn('complaintid',$resolveid)->get()->unique('complaintid')->count();
              if (request()->has('type')) {
                 $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 1])->whereIn('complaintid',$complaintid)->whereIn('complaintid',$resolveid)->get()->unique('complaintid');
           } else {
            $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->orderBy('id', 'DESC')->get()->unique('complaintid');
            }
            $departments = Department::with('users')->get();
             if($req->product_namesearch)
            {
            $complainproduct = Complaint::where('product_nameid',$req->product_namesearch)->pluck('id')->all();
            $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0])->whereIn('complaintid',$complainproduct)->orderBy('id', 'DESC')->get()->unique('complaintid');
            }
              if (request()->has('startdate') && request()->has('enddate')) {

                $from = strtotime(request()->startdate);
                $from = date("Y-m-d", $from);
                $to = strtotime(request()->enddate);
                $to = date("Y-m-d", $to);
                // dd($from);
               // $data=[];
                                       $complaintid = Complaint::whereRaw("(updated_at >= ? AND updated_at <= ?)", [$from." 00:00:00", $to." 23:59:59"])->pluck('id')->all();
                 $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->orderBy('id', 'DESC')->get()->unique('complaintid');
                $data6 = Complaint::whereRaw("(created_at >= ? AND created_at <= ?)", [$from." 00:00:00", $to." 23:59:59"])->get();
            }
            else if (request()->has('type')) {
            
            
            
                if (request()->get('type') == 'resolved') {
                 //$data = $data->where('is_resolved', 1);
                           $data = Transition::where(['is_transfered' => 0, 'is_resolved' => 1])->whereIn('complaintid',$resolveid)->orderBy('id', 'DESC')->get()->unique('complaintid');
                 $data6 = Complaint::whereNotIn('id',$resolveid)->whereIn('id',$resolveid)->get();
                  ////  $data = Resolution::whereIn('complaint_id',$complaintid)->whereIn('complaint_id',$resolvedcomplaintsonly)->get()->unique('complaint_id')->count();
                  /////  $data = Resolution::whereIn('complaint_id',$complaintid)->whereNotIn('complaint_id',$resolvedcomplaintsonly)->get()->unique('complaint_id')->count();
                  
 
                 
                }
                if (request()->get('type') == 'pending') {
                    $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->orderBy('id', 'DESC')->get()->unique('complaintid');
                
                 $data6 = Complaint::whereNotIn('id',$resolveid)->whereNotIn('id',$resolveid)->get();

                }
                if (request()->get('type') == 'crossedtl') {

                    $data = $data->where('is_resolved', 0)->whereIn('complaintid',$complaintid)->whereNotIn('complaintid',$resolveid)->whereHas('complaint', function ($q) {
                        $getdays = complaintlimit();
                        $q->whereDate('created_at','<', Carbon::now()->subDays($getdays)->format('Y-m-d'));
                        
                    })->with('complaint');
                                     

                                $data = $data->orderBy('id', 'DESC')->get()->unique('complaintid');
                                   $data6 = Complaint::whereNotIn('id',$resolveid)->whereNotIn('id',$resolveid)->whereDate('created_at','<', Carbon::now()->subDays(complaintlimit())->format('Y-m-d'))->get();
                }
            } 
             else if (request()->has('cmpsource')) {
              $cmpsource = request()->cmpsource;
                
                       $complaintid = Complaint::where('complaintsource',$cmpsource)->pluck('id')->all();
                    //  dd($complaintid);
                      $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->orderBy('id', 'DESC')->get()->unique('complaintid');
               
                $data6 = [];
                
            }
if(isset($req->export))
                {
        //   dd('call2');
          //  dd(request()->get('type'));
           if($req->product_namesearch)
            {
            $complainproduct = Complaint::where('product_nameid',$req->product_namesearch)->pluck('id')->all();
            $complainproduct1 = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0])->whereIn('complaintid',$complainproduct)->orderBy('id', 'DESC')->get()->unique('complaintid')->pluck('complaintid');
            $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereIn('id',$complainproduct1)->orderBy('id', 'desc')->get();

     return Excel::download(new OtherExport($data), 'Complaints.xlsx');
            }
                    elseif (request()->get('type') == 'resolved')
                     { $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereIn('id',$resolveid)->orderBy('id', 'desc')->get();
     return Excel::download(new OtherExport($data), 'Complaints.xlsx');
}
elseif(request()->get('type') == 'pending')
{
          $complaintid2 = Complaint::pluck('id')->all();
          $complaintid1 = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid2)->get()->unique('complaintid')->pluck('complaintid');
 $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereIn('id',$complaintid1)->orderBy('id', 'desc')->get();

     return Excel::download(new OtherExport($data), 'Complaints.xlsx');
}
elseif (request()->get('type') == 'crossedtl') {
                    $getdays = complaintlimit();

                     $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereNotIn('id',$resolveid)->whereDate('created_at','<', Carbon::now()->subDays($getdays)->format('Y-m-d'))->where('createdby', auth()->user()->id)->orderBy('id', 'desc')->get();
         return Excel::download(new OtherExport($data), 'Complaints.xlsx');

                   
                }
                         elseif ($req->startdate) {
                     //   dd('call2');
                             $fromDate = Carbon::parse(request()->startdate)->format('Y-m-d');
                   // dd($fromDate);

                    $toDate = Carbon::parse(request()->enddate)->format('Y-m-d');
                     $complaintid2 = Complaint::pluck('id')->all();
          $complaintid1 = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid2)->get()->unique('complaintid')->pluck('complaintid'); $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereDate('updated_at','>=',$fromDate)->whereDate('updated_at','<=',$toDate)->whereIn('id',$complaintid1)->orderBy('id', 'desc')->get();
                    return Excel::download(new OtherExport($data), 'Complaints.xlsx');
                         
                         } 
         
        elseif ($req->cmpsource) {
                       //  dd('call4');
        $cmpsource = request()->cmpsource;
                       $complaintid = Complaint::where('complaintsource',$cmpsource)->pluck('id')->all();
                    //  dd($complaintid);
                      $complaintid2 = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->get()->unique('complaintid')->pluck('complaintid');
               
        $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','createdby','title','is_resolved','email')->whereIn('id',$complaintid2)->orderBy('id', 'desc')->get();

        return Excel::download(new OtherExport($data), 'Complaints.xlsx');

                }
               else
               {
                $complaintid2 = Complaint::pluck('id')->all();
          $complaintid1 = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid2)->get()->unique('complaintid')->pluck('complaintid');
 $data=Complaint::select('uuid','customername','mobile','details','complainttype','complaintsource','created_at','createdby','title','is_resolved','email')->whereIn('id',$complaintid1)->orderBy('id', 'desc')->get();

     return Excel::download(new OtherExport($data), 'Complaints.xlsx');
               }
                }
            return view('seniorlevel.dashboard', compact('countactive', 'countresolved', 'data', 'departments','complaintsource','category','product'));
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function complaint()
    {
        try {
            // dd(auth()->user()->id);
            $complaintid = Complaint::pluck('id')->all();
            $data = Transition::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->whereIn('complaintid',$complaintid)->orderBy('id', 'DESC')->get()->unique('complaintid');
            $departments = Department::with('users')->get();
            return view('seniorlevel.complaint', compact('data', 'departments'));
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function resolvecomplaintslevel(Request $request)
    {
        try {
            // dd($request);
             $mydata = Complaint::find($request->id);
                  $mydata->is_resolved = 1;
      
                  $mydata->save();
            $fromuser = $request->fromuser;
            $departmentid = $request->departmentid;
            Resolution::create(['complaint_id' => $request->id, 'resolution' => $request->resolution, 'user_id' => auth()->user()->id]);
            $datecreated = Transition::where(['complaintid' => $request->id, 'touser' => auth()->user()->id])->first();
            // $datecreated = date_create($datecreated->created_at->format('Y-m-d'));
            // $resolvedate = date_create(date('Y-m-d'));
            // $diff=date_diff($datecreated,$resolvedate);
             Transition::where('complaintid', $request->id)->update(['is_resolved' => 1]);
            $today = date('Y-m-d H:i:s');
            $date_of_quote = $datecreated->created_at->format('Y-m-d H:i:s');

            $date1 = strtotime($today);
            $date2 = strtotime($date_of_quote);

            $diff = abs($date1 - $date2);

            // To get the year divide the resultant date into
            // total seconds in a year (365*60*60*24)
            $years = floor($diff / (365 * 60 * 60 * 24));

            // To get the month, subtract it with years and
            // divide the resultant date into
            // total seconds in a month (30*60*60*24)
            $months = floor(($diff - $years * 365 * 60 * 60 * 24)
                / (30 * 60 * 60 * 24));

            $days = floor(($diff - $years * 365 * 60 * 60 * 24 -
                $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            //echo $days;

            $getdays = getlimitdays(auth()->user()->id);
            $doc = '';
            if ($request->has('document')) {
                $foldername = 'resolve_document';
                $doc = $request->file('document')->store($foldername, 'public');

            }

            if ($days <= $getdays) {
                ComplaintEvaluation::create(['document' => $doc, 'complaintid' => $request->id, 'is_ontime' => 1, 'is_senior' => 1]);
            } else {
                ComplaintEvaluation::create(['document' => $doc, 'complaintid' => $request->id, 'is_ontime' => 0, 'is_senior' => 1]);
            }


            $getemails = ActionTriggers::where('action_id', 5)->where('is_email', 1)->pluck('role');
            $getsms = ActionTriggers::where('action_id', 5)->where('is_sms', 1)->pluck('role');
            $complaint = Complaint::where('id', $request->id)->first();
            $createdbyuser = User::where('id', $complaint->createdby)->first();
            $toemails = [];
            $tosms = [];
            $tousers = [];
            if (count($getemails)) {
                foreach ($getemails as $value) {
                    switch ($value) {
                        case 1:
                            $getuser = User::where('id', $complaint->createdby)->first();
                            if ($getuser) {
                                array_push($toemails, $getuser->email);
                            }
                            break;

                        case 2:
                            $getuser = User::where('id', $fromuser)->first();
                            if ($getuser) {
                                array_push($toemails, $getuser->email);
                            }
                            break;

                        case 3:
                            array_push($toemails, auth()->user()->email);
                            break;

                        case 4:
                            $getuser = User::where('role', $value)->first();
                            if ($getuser) {
                                array_push($toemails, $getuser->email);
                            }
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            }

            if (count($getsms)) {
                foreach ($getsms as $value) {
                    switch ($value) {
                        case 1:
                            $getuser = User::where('id', $complaint->createdby)->first();
                            if ($getuser) {
                                array_push($tosms, $getuser->mobile);
                            }
                            break;

                        case 2:
                            $getuser = User::where('id', $fromuser)->first();
                            if ($getuser) {
                                array_push($tosms, $getuser->mobile);
                            }
                            break;

                        case 3:
                            array_push($tosms, auth()->user()->mobile);
                            break;

                        case 4:
                            $getuser = User::where('role', $value)->first();
                            if ($getuser) {
                                array_push($tosms, $getuser->mobile);
                            }
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            }

            if (count($getemails)) {
                foreach ($getemails as $value) {
                    switch ($value) {
                        case 1:
                            $getuser = User::where('id', $complaint->createdby)->first();
                            if ($getuser) {
                                array_push($tousers, $getuser->id);
                            }
                            break;

                        case 2:
                            $getuser = User::where('id', $fromuser)->first();
                            if ($getuser) {
                                array_push($tousers, $getuser->id);
                            }
                            break;

                        case 3:
                            array_push($tousers, auth()->user()->id);
                            break;

                        case 4:
                            $getuser = User::where('role', $value)->first();
                            if ($getuser) {
                                array_push($tousers, $getuser->id);
                            }
                            break;

                        default:
                            # code...
                            break;
                    }
                }
            }
            if (count($tousers)) {
                foreach ($tousers as $key => $value) {
                    Notification::create(['userid' => $value, 'message' => '<b> ' . $complaint->title . ' </b> complaint has been resolved.']);
                }
            }
            ComplaintResolved::dispatchNow($toemails, $complaint->mobile, $complaint->title, $complaint->customername, $complaint->uuid, auth()->user()->name, $request->resolution,auth()->user()->mobile);
            RatingMail::dispatchNow($complaint->customername, $complaint->mobile, $complaint->email);

            event(new AppComplaintResolved($complaint->title));
            return redirect()->route('scomplaint');
        } catch (\Throwable $th) {
           // dd($th);
        }
    }

    public function inquiry1()
    {
        try {
            $data = InquiryTransactions::where(['touser' => auth()->user()->id, 'is_transfered' => 0, 'is_resolved' => 0])->orderBy('id', 'DESC')->get();
            $departments = Department::with('users')->get();
            return view('seniorlevel.inquiry')->with(['data' => $data, 'departments' => $departments]);
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }
    public function inquiry(Request $req)
    {
        try {
            $inquirysource = InquiryType::all();
            $users = User::all();

            $data24 = InquiryTransactions::where(['touser' => auth()->user()->id, 'is_transfered' => 0])->orderBy('id', 'DESC')->pluck('inquiryid')->all();
                                             $data =   Inquiry::whereIn('id', $data24)->get();
            $resolveinquiry = InquiryResolution::whereIn('id',$data24)->pluck('inquiry_id')->all();

            $departments = Department::with('users')->get();
            if (request()->has('startdate') && request()->has('enddate')) {
                $from = strtotime(request()->startdate);
                $from = date("Y-m-d", $from);
                $to = strtotime(request()->enddate);
                $to = date("Y-m-d", $to);
                $data = Inquiry::whereIn('id',$data24)->whereRaw(
                    "(created_at >= ? AND created_at <= ?)",
                    [$from . " 00:00:00", $to . " 23:59:59"]
                )->orderBy('id', 'desc')->get();
                        } 
            elseif ($req->type) {
           
                if ($req->type == 'resolved') {
                    $data =   Inquiry::whereIn('id',$resolveinquiry)->get();

                    //////// $resolvedinquiries = InquiryTransactions::where('is_resolved', 1)->whereRaw("(updated_at >= ? AND updated_at <= ?)", [$from . " 00:00:00", $to . " 23:59:59"])->get()->unique('inquiryid');
                     // dd($resolvedinquiries);
                    
                
                      }
                if ($req->type == 'pending') {
                
                    $data =   Inquiry::whereIn('id',$data24)->whereNotIn('id',$resolveinquiry)->get();

                   
                    if(isset($req->export))
                    {
                       ////dd('call');
                $data=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->whereNotIn('id', $resolveinquiry)->orderBy('id', 'desc')->get();
                return Excel::download(new OtherInquiryExport($data), 'Inquiries.xlsx');
           
                    }
                }
               
            } 
            else if ($req->inqsource) {
                $data =   Inquiry::where('inquirysource',$req->inqsource)->whereIn('id',$data24)->get();
                 //// $data = Inquiry::where('inquirysource',$req->inqsource)->orderBy('id', 'desc')->get();

            }
           

              if(isset($req->export))
             {

                 if ($req->type == 'resolved') {
                     $data12=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->whereIn('id', $resolveinquiry)->orderBy('id', 'desc')->get();
                  //   dd($data);
         return Excel::download(new OtherInquiryExport($data12), 'Inquiries.xlsx');
                 }
                 elseif($req->type == 'pending')
                 {
                              ////dd($data);
                     $data12=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->whereIn('id',$data24)->whereNotIn('id', $resolveinquiry)->orderBy('id', 'desc')->get();
                     return Excel::download(new OtherInquiryExport($data12), 'Inquiries.xlsx');
                     
                 }
                 elseif($req->inqsource)
                 {
                //    dd('call');

                     $data12=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->where('inquirysource', $req->inqsource)->whereIn('id', $data24)->orderBy('id', 'desc')->get();
                     return Excel::download(new OtherInquiryExport($data12), 'Inquiries.xlsx');
                     
                 }
                
                 elseif ($req->startdate && $req->enddate) {
                     $fromDate = Carbon::parse(request()->startdate)->format('Y-m-d');
                    
                     $toDate = Carbon::parse(request()->enddate)->format('Y-m-d');
                //////////////dd($fromDate);
                   $data12=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->whereRaw(
                         "(created_at >= ? AND created_at <= ?)",
                         [$fromDate . " 00:00:00", $toDate . " 23:59:59"]
                     )->whereIn('id',$data24)->orderBy('id', 'desc')->get();
                     return Excel::download(new OtherInquiryExport($data12), 'Inquiries.xlsx');
 
                    // dd($data1);
                 }
                 else
                 {
                     $data12=Inquiry::select('uuid','customername','contact','details','inquirysource','created_at','createdby','email')->whereIn('id',$data24)->orderBy('id', 'desc')->get();
                     return Excel::download(new OtherInquiryExport($data12), 'Inquiries.xlsx');
               
 
                 }
             }
            
            return view('seniorlevel.inquiry')->with(['data' => $data, 'departments' => $departments, 'inquirysource'=> $inquirysource, 'users'=> $users]);
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }
    public function resolveinquiryslevel(Request $request)
    {
        try {
            $fromuser = $request->fromuser;
            $departmentid = $request->departmentid;
            InquiryResolution::create(['inquiry_id' => $request->id, 'resolution' => $request->resolution]);
            $datecreated = InquiryTransactions::where(['inquiryid' => $request->id, 'touser' => auth()->user()->id])->first();
            // $datecreated = date_create($datecreated->created_at->format('Y-m-d'));
            // $resolvedate = date_create(date('Y-m-d'));
            // $diff=date_diff($datecreated,$resolvedate);

            $today = date('Y-m-d H:i:s');
            $date_of_quote = $datecreated->created_at->format('Y-m-d H:i:s');

            $date1 = strtotime($today);
            $date2 = strtotime($date_of_quote);

            $diff = abs($date1 - $date2);

            // To get the year divide the resultant date into
            // total seconds in a year (365*60*60*24)
            $years = floor($diff / (365 * 60 * 60 * 24));

            // To get the month, subtract it with years and
            // divide the resultant date into
            // total seconds in a month (30*60*60*24)
            $months = floor(($diff - $years * 365 * 60 * 60 * 24)
                / (30 * 60 * 60 * 24));

            $days = floor(($diff - $years * 365 * 60 * 60 * 24 -
                $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            //echo $days;

            $getdays = getlimitdays(auth()->user()->id);
            if ($request->has('document')) {
                $foldername = 'resolve_document';
                $doc = $request->file('document')->store($foldername, 'public');

            }
            if ($days <= $getdays) {
                InquiryEvaluation::create(['document' => $doc, 'inquiryid' => $request->id, 'is_ontime' => 1, 'is_senior' => 1]);
            } else {
                InquiryEvaluation::create(['document' => $doc, 'inquiryid' => $request->id, 'is_ontime' => 0, 'is_senior' => 1]);
            }
            // InquiryTransactions::where('inquiryid', $request->id)->update(['is_resolved' => 1]);

            $inquiry = Inquiry::where('id', $request->id)->first();
            $createdbyuser = User::where('id', $inquiry->createdby)->first();
            $ceoemail = User::where('role', 4)->first();
            $caller = User::where('role', 1)->first();
            if ($ceoemail) {
                $ceo = $ceoemail->email;
                $emails = [auth()->user()->email, $ceo, $caller->email];
                $mobiles = [auth()->user()->mobile, $ceoemail->mobile, $caller->mobile];
                $toids = [auth()->user()->id, $ceoemail->id, $caller->id];
                foreach ($toids as $key => $value) {
                    Notification::create(['userid' => $value, 'message' => 'An inquiry has been resolved. Please check the portal for <b> ' . $inquiry->uuid . ' </b> details.']);
                }
            } else {
                $emails = [auth()->user()->email];
                $mobiles = [auth()->user()->mobile];
            }
            // InquiryResolved::dispatch($emails, $mobiles, 'Inquiry', $request->name, $request->uuid, auth()->user()->name, $request->resolution);
            RatingMail::dispatchNow($inquiry->customername, $inquiry->mobile, $inquiry->email);

            event(new InquiryResolvedEvent($inquiry->uuid));
            InquiryResolved::dispatchNow($emails, $mobiles, 'Inquiry', $createdbyuser->name, $inquiry->uuid, auth()->user()->name, $request->resolution);
            return redirect()->route('sinquiry');
        } catch (\Throwable $th) {
            // dd($th);
        }
    }

    public function closecomplaints()
    {
        try {
            $resolveid = Resolution::where('user_id',auth()->user()->id)->pluck('complaint_id')->all();
          //  $resolveid = Resolution::pluck('complaint_id')->all();
            $data = Transition::where(['is_transfered' => 0,'is_resolved' => 1])->whereIn('complaintid',$resolveid)->orderBy('id', 'DESC')->get()->unique('complaintid');
            return view('seniorlevel.closecomplaints')->with('data', $data);
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function updateclosecomplaint(Request $request)
    {
        try {
            Transition::where('complaintid', $request->id)->update(['is_resolved' => 1]);
            $getcustomercontact = Complaint::find($request->id);
            $mobile = [$getcustomercontact->mobile];

            RatingMail::dispatchNow($getcustomercontact->customername, $getcustomercontact->mobile, $getcustomercontact->email);

            // $url = env('APP_URL').'/trackcomplaint';
            // $this->sendsms($mobile,'Your complaint "'.$getcustomercontact->title.'" has been resolved. You can check the status of your complaint using your mobile number at '.$url);
            return redirect()->back();
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function closeinquirys()
    {
        try {
            $data = InquiryTransactions::where(['touser' => auth()->user()->id, 'is_transfered' => 0,'is_resolved' => 1])->orderBy('id', 'DESC')->get()->unique('inquiryid');
            $frontinq = Inquiry::has('inquiryresolutionrelation')->get();
            return view('seniorlevel.closeinquirys')->with(['data' => $data, 'frontinq' => $frontinq]);
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function updatecloseinquirys(Request $request)
    {
        try {
            InquiryTransactions::where('inquiryid', $request->id)->update(['is_resolved' => 1]);
            return redirect()->back();
        } catch (\Throwable $th) {
            $this->customerr($th);
        }
    }

    public function showprofile()
    {
        try {
            return view('seniorlevel.showprofile');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function changepassword()
    {
        try {
            return view('seniorlevel.changepassword');
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
