@extends('seniorlevel.layout.slevelapp')
@section('content')
<style>
.feather {
    font-family: 'feather' !important;
    speak: none;
    font-style: normal;
    font-weight: normal;
    font-variant: normal;
    text-transform: none;
    line-height: 1;
    -webkit-font-smoothing: antialiased;
    font-size: 22px;
    -moz-osx-font-smoothing: grayscale;
}
table.table-bordered.dataTable tbody th {
    white-space: normal !important;
    /* display: inline-block; */
    max-width: 132px;
}
    </style>
    <script src="{{URL::asset('files\assets\js\tabpanel-custom.js')}}"></script>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-2">
        <a href="{{route('sdashboard')}}" class="btn btn-primary">Remove Filters</a>
    </div>
    <div class="col-md-3" style="text-align: center;">
        <div class="dropdown-primary dropdown open">
            <button class="btn btn-primary dropdown-toggle waves-effect waves-light " type="button" id="dropdown-2"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                @switch(request()->get('type'))
                @case('resolved')
                    Resolved Complaints
                    @break
                @case('crossedtl')
                    Crossed Timeline
                    @break
                @case('pending')
                    Pending Complaints
                    @break
                @default
                    All Complaints
            @endswitch
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                data-dropdown-out="fadeOut" x-placement="top-start"
                style="position: absolute; transform: translate3d(0px, -2px, 0px); top: 0px; left: 0px; will-change: transform;">
                <a class="dropdown-item waves-light waves-effect" href="{{ request()->fullUrlWithQuery(['type' => 'crossedtl'])}}">Complaints with Crossed
                    Timeline</a>
                    <a class="dropdown-item waves-light waves-effect" href="{{ request()->fullUrlWithQuery(['type' => 'pending'])}}">Pending Complaints</a>
                    <a class="dropdown-item waves-light waves-effect" href="{{ request()->fullUrlWithQuery(['type' => 'resolved'])}}">Resolved Complaints</a>
                    <a class="dropdown-item waves-light waves-effect" href="{{route('sdashboard')}}">All Complaints</a>
            </div>
        </div>
    </div>
   


           <div class="col-md-3">
        <div class="dropdown-primary dropdown open">
            <button class="btn btn-primary dropdown-toggle waves-effect waves-light " type="button" id="dropdown-2"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Complaint Source</button>
            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                data-dropdown-out="fadeOut" x-placement="top-start"
                style="position: absolute; transform: translate3d(0px, -2px, 0px); top: 0px; left: 0px; will-change: transform; height:200px; overflow-y:scroll;">
                @if (count($complaintsource))
                @foreach ($complaintsource as $item)
                <a class="dropdown-item waves-light waves-effect" href="{{ request()->fullUrlWithQuery(['cmpsource' => $item->id])}}">{{$item->name}}</a>
                @endforeach
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <form method="GET" id="datefilter">
            <div id="reportrange"
                style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                <i class="fa fa-calendar"></i>&nbsp;
                @if (request()->has('startdate') && request()->has('enddate'))
                    <input type="hidden" id="checkfirst" value="0">
                    <span id="daterange">{{date('F j, Y', strtotime(request()->get('startdate')))}} - {{date('F j, Y', strtotime(request()->get('enddate')))}}</span>
                    @else
                    <input type="hidden" id="checkfirst" value="1">
                    <span id="daterange"></span>
                @endif
                <i class="fa fa-caret-down"></i>
            </div>
            <input type="hidden" id="startdate" name="startdate" value="" />
            <input type="hidden" id="enddate" name="enddate" value="" />
            @if (request()->has('employee'))
            <input type="hidden" id="employee" name="employee" value="{{request()->get('employee')}}" />
            @endif
            @if (request()->has('type'))
            <input type="hidden" id="type" name="type" value="{{request()->get('type')}}" />
            @endif
        </form>
        </div>
        
         <form id="date-filter"  action="{{route('sdashboard')}}">
                                     <div class="row" style="float:left;">
        <div class="col-md-5" style="text-align: center;margin-top: 20px;">
        <select id="p_c" name="p_c" class="form-control" onchange="getcategorysearch(this.value)">
                                                    <option value="">Select Product Category</option>
                                                    @foreach ($category as $item)
                                                     @if($item->id == request()->get('p_c'))
                                           @php $selected2 = 'selected="se;ected"'; @endphp
                                           @else
                                           @php $selected2 = ''; @endphp
                                           @endif
                                                    <option {{$selected2}}  value="{{$item->id}}">{{ucwords($item->name ?? 'N/A')}}</option>
                                                    @endforeach
                                                   </select>
    </div>
    <div class="col-md-5" style="text-align: center;margin-top: 20px;">
        <select  id="product_namesearch"   name="product_namesearch" data-placeholder="Select Product Name" class="form-control select">
                                                 <option value="">Select Product Name</option>
                                                 @foreach ($product as $pitem)
                                                  @if($pitem->id == request()->get('product_namesearch'))
                                           @php $selected2 = 'selected="se;ected"'; @endphp
                                           @else
                                           @php $selected2 = ''; @endphp
                                           @endif
                                                 <option  {{$selected2}}  value="{{$pitem->id}}">{{ucwords($pitem->name ?? 'N/A')}}</option>
                                                  @endforeach
                                                    </select>
    </div>
      <div class="col-md-2" style="text-align: center;margin-top: 20px;">
                                    <button class="btn btn-primary" type="submit">Filter Product</button>
                                </div>
                                </div>
                                </form>
                                <form id="date-filter"  action="{{route('sdashboard')}}" style="margin-top:20px;">
                                          <input type="hidden" id="startdate" name="startdate" value="<?php echo request()->get('startdate');?>">
                                             <input type="hidden" id="product_namesearch" name="product_namesearch" value="<?php echo request()->get('product_namesearch');?>">
                                          <input type="hidden" id="enddate" name="enddate" value="<?php echo request()->get('enddate');?>">
          <input type="hidden" id="cmpsource" name="cmpsource" value="<?php echo request()->get('cmpsource');?>">
                                          <input type="hidden" id="type" name="type" value="<?php echo request()->get('type');?>">
                                                                     <input type="hidden" id="employee" name="employee" value="<?php echo request()->get('employee');?>">
                          <input type="submit" style="margin-left:45px;" class="btn btn-primary" name="export" value="Export to Excel">

                                 </form>
    </div>
</div>


<div class="container row">
    <a class="col-xl-6 col-md-6" href="{{route('sdashboard')}}">
        <div class="card bg-c-pink text-white">
            <div class="card-block">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="m-b-5">Active Complaints</p>
                        <h4 class="m-b-0">{{$countactive}}</h4>
                    </div>
                    <div class="col col-auto text-right">
                        <i class="feather icon-user f-50 text-c-pink"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
    <a class="col-xl-6 col-md-6" href="{{route('sdashboard',['type'=>'closed'])}}">
        <div class="card bg-c-green text-white">
            <div class="card-block">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="m-b-5">Resolved Complaints</p>
                        <h4 class="m-b-0">{{$countresolved}}</h4>
                    </div>
                    <div class="col col-auto text-right">
                        <i class="feather icon-user f-50 text-c-yellow"></i>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<div class="row">
    <div class="col-sm-12">
        <!-- Zero config.table start -->
        <div class="card">
            <div class="card-header">
                <h5>Complaint</h5>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    <table id="simpletable" class="table table-striped table-bordered nowrap complainttable">
                        <thead>
                            <tr>
                                <!--<th>S.No</th>-->
                                <!--<th>C.No</th>---->
                                <th>Customer Name</th>
                                <th>Mob</th>
                                <th>Registered by</th>
                                  <th>Created at</th>
                                <th>Complaint Details</th>
                                <th>Remaining Days</th>
                                {{-- <th>Transfer</th> --}}
                                <th>Resolve</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $key => $item)
                            {{-- {{dd($item)}} --}}
                            @if(!is_null($item->complaint))
                            <tr>
                               <!-- <td>{{$key + 1}}</td>---->
                                <!---<td>{{$item->complaint->uuid ?? 'N/A'}}</td>---->
                                <td>{{$item->complaint->customername ?? 'N/A'}}</td>
                                <td>{{$item->complaint->mobile ?? 'N/A'}}</td>
                                <th>{{ App\User::find($item->complaint->createdby)->email ?? 'N/A' }}</th>
                                                                <th>{{$item->complaint->created_at ?? 'N/A'}}</th>
@php $Resolvedd=App\Models\Resolution::where('complaint_id',$item->complaint->id)->orderBy('id', 'desc')->first(); @endphp
                                  <th>
                                    <div class="animation-model">
                                        <button type="button"
                                            class="btn btn-primary btn-outline-primary waves-effect md-trigger detailsmodalbtn"
                                            data-toggle="modal" data-target="#detailsmodal" data-details="{{$item}}"
                                            @if ($item->complaint->image)
                                            data-img='<img src="{{\Storage::disk('public')->url(optional($item->complaint)->image)}}" style="max-width: 300px;max-height: 200px;"/>' data-Resolve="{{optional($Resolvedd)->resolution}}"
                                            data-imglink="{{\Storage::disk('public')->url(optional($item->complaint)->image)}}"
                                                    @else
                                      

                                    @php $complaintattachment=App\Models\ComplaintAttachment::where('complaint_id', $item->complaint->id)->where('media_type', '0')->orderByDesc('id')->get();
 @endphp

                                    @if(!empty($complaintattachment))
                                    @php $ii= count($complaintattachment); 
                                                 for ($i = count($complaintattachment) - 1; $i >= 0; $i--) {
@endphp
 data-img='<img src="{{\Storage::disk('public')->url($complaintattachment[$i]->media_name)}}" style="max-width: 300px;max-height: 200px;"/>'  data-Resolve="{{optional($Resolvedd)->resolution}}"
                                            data-imglink="{{\Storage::disk('public')->url($complaintattachment[$i]->media_name)}}"

                                       @php $ii--;} @endphp
                                   @else
                                   data-img="No Image"
                                    data-imglink=""
                                    @endif


                                    @endif
                                            >
                                            Details
                                        </button>
                                    </div>
                                </th>
                                <td>{{today()->diffInDays($item->created_at->addDays(2))}}</td>
                            {{-- <td><select class="js-example-basic-single col-sm-12 onfff" id="{{$item->id}}" >
                                    <option >Click to Select</option>
                                    @foreach ($departments as $department)
                                    <optgroup label="{{$department->name}}">
                                        @foreach ($department->users as $user)
                                            @if ($user->id != auth()->user()->id)
                                             <option value="{{$user->id}}" >{{$user->name}}</option>
                                            @endif
                                        @endforeach
                                    </optgroup>
                                    @endforeach


                                    </select>
                                </td> --}}
                               <td>
                                   @if (checkifresolved(optional($item->complaint)->id))
                                    Resolved
                                    @else
                                    <button type="button" class="btn btn-primary waves-effect complaintresolvebtn"
                                        data-id="{{$item->complaintid}}" data-fromuser="{{$item->fromuser}}"
                                        data-departmentid="{{$item->departmentid}}" data-toggle="modal"
                                        data-target="#resolve-Modal">
                                        Resolve
                                    </button>
                                    @endif
                                </td>
                            </tr>
                               @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Zero config.table end -->
    </div>
</div>
<div class="modal fade" id="detailsmodal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Details of the Complaint</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    <span class="text-primary modalspan">Customer name:</span> <span class="modalspan" id="custname"></span> <br>
                    <span class="text-primary modalspan">Mobile number:</span> <span class="modalspan" id="custcontact"></span> <br>
                    <span class="text-primary modalspan">Product Image:</span> <span class="modalspan" id="custimg"></span> <br>
                    <span class="text-primary modalspan"><a href="" id="imglink" target="_blank">Click here to see the full image</a></span> <br>
                    <span class="text-primary modalspan">Details:</span> <span class="modalspan" id="custdetails"></span></br>
                    <span class="text-primary modalspan">Resolution:</span> <span class="modalspan" id="custresolve"></span>
                  
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary waves-effect" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="resolve-Modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data" action="{{ route('resolvecomplaintslevel') }}">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Resolution</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        <input type="hidden" name="id" id="complaintid">
                        <input type="hidden" name="fromuser" id="fromuser">
                        <input type="hidden" name="departmentid" id="departmentid">
                        <div class="form-group">
                                        <label>Document</label>
                                            <input style="margin-left: 130px;" type="file" id="document" name="document"  />
                                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Details of resolution</label>
                            <div class="col-sm-9">
                                <textarea rows="5" cols="5" class="form-control" name="resolution"
                                    id="resolution" minlength="50" ></textarea>
                            </div>
                        </div>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light submitinquiryresolution">Save
                        changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script type="text/javascript">
    $(function() {
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            var startsend = picker.startDate.format('L');
            var endsend = picker.endDate.format('L');
            $('#startdate').val(startsend);
            $('#enddate').val(endsend);
            $('#datefilter').submit();
        });
        var start = moment().subtract(29, 'days');
        var end = moment();

        function cb(start, end) {
            $('#daterange').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start
            , endDate: end
            , ranges: {
                'Today': [moment(), moment()]
                , 'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]
                , 'Last 7 Days': [moment().subtract(6, 'days'), moment()]
                , 'Last 30 Days': [moment().subtract(29, 'days'), moment()]
                , 'This Month': [moment().startOf('month'), moment().endOf('month')]
                , 'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        var checkfirst = $('#checkfirst').val();
        if (parseInt(checkfirst)) {
            cb(start, end);
        }
        

    });
    $(document).ready(function(){

///////alert("call");
<?php
if(request()->get('product_namesearch'))
{
?>
  var selectcatt = <?php echo request()->get('product_namesearch');?>;
<?php
}
?>

  //alert(selectcatt);
    getcategorysearchfilter(<?php echo request()->get('p_c');?>);
    
     function getcategorysearchfilter(val)
 {
//alert(val);


    $.ajax({
            type: 'GET',
            url: "{{url('frontoffice/get_category')}}"+'?id='+val,
            success: function (resp) {
           resp=JSON.parse(resp);
                console.log(resp);
            var string="";
            $('#product_namesearch').html('');
            string+='<option value="">Select Product Name</option>';
            for(i=0;i<resp.length;i++)
            {
           // console.log(resp[i].id);
            if(resp[i].id==selectcatt)
            {
              string+='<option selected value="'+resp[i].id+'">'+resp[i].name+'</option>';
               

             }
             else
             {
              string+='<option  value="'+resp[i].id+'">'+resp[i].name+'</option>';
             }

             
            }
              
             $('#product_namesearch').html(string);
           }
            });
    
 }
 });
 function getcategorysearch(val)
 {

    $.ajax({
            type: 'GET',
            url: "{{url('frontoffice/get_category')}}"+'?id='+val,
            success: function (resp) {
           resp=JSON.parse(resp);
                console.log(resp);
            var string="";
            $('#product_namesearch').html('');
            string+='<option value="">Select Product Name</option>';
            for(i=0;i<resp.length;i++)
            {
                string+='<option value="'+resp[i].id+'">'+resp[i].name+'</option>';
            }
              
             $('#product_namesearch').html(string);
           }
            });
    
 }
</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function(){
        $('.detailsmodalbtn').each(function(){
            $(this).click(function(){
                var details = $(this).attr('data-details');
             //// alert(details);
              
                var img = $(this).attr('data-img');
                var resolvedd = $(this).attr('data-Resolve');

                var imglink = $(this).attr('data-imglink');
                var comdet = JSON.parse(details);
                $('#custname').html(comdet['complaint']['customername']);
                $('#custcontact').html(comdet['complaint']['mobile']);
                if(img)
                {
                $('#custimg').html(img);
                if (imglink) {
                    $('#imglink').show();
                    $('#imglink').attr('href', imglink);
                } else {
                    $('#imglink').hide();
                }
                }
                else
                {
                
                                $('#custimg').html('No Image');

                }
                $('#custdetails').html(comdet['complaint']['details'])
               $('#custresolve').html(resolvedd)

            });
        });

    });
</script>
@endsection
