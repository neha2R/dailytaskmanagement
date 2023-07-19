@extends('ceo.layout.app')
@section('content')
<style>
    .modalspan{
        padding: 5px;
    }
</style>
<script src="{{URL::asset('files\assets\js\tabpanel-custom.js')}}"></script>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-md-2">
        <a href="{{route('ceocomplaint')}}" class="btn btn-primary">Remove Filters</a>
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
                    <a class="dropdown-item waves-light waves-effect" href="{{route('ceocomplaint')}}">All Complaints</a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="dropdown-primary dropdown open">
            <button class="btn btn-primary dropdown-toggle waves-effect waves-light " type="button" id="dropdown-2"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filter by Employees</button>
            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn"
                data-dropdown-out="fadeOut" x-placement="top-start"
                style="position: absolute; transform: translate3d(0px, -2px, 0px); top: 0px; left: 0px; will-change: transform; height:200px; overflow-y:scroll;">
                @if (count($users))
                @foreach ($users as $item)
                <a class="dropdown-item waves-light waves-effect" href="{{ request()->fullUrlWithQuery(['employee' => $item->id])}}">{{$item->name}}</a>
                @endforeach
                @endif
            </div>
        </div>
    </div>
        <input type="hidden" id="cmpsource" name="cmpsource" value="<?php echo request()->get('cmpsource');?>">

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
    <div class="col-md-4" style="margin-top:20px;">
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
            <form id="date-filter"  action="{{route('ceocomplaint')}}">
                                     <div class="row" style="float:left;">
        <div class="col-md-10" style="text-align: center;margin-top: 20px;">
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
    <div class="col-md-10" style="text-align: center;margin-top: 20px;">
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
                                <form id="date-filter"  action="{{route('totalcomplaints')}}" style="margin-top:20px;">
                                          <input type="hidden" id="startdate" name="startdate" value="<?php echo request()->get('startdate');?>">
                                          <input type="hidden" id="enddate" name="enddate" value="<?php echo request()->get('enddate');?>">
          <input type="hidden" id="cmpsource" name="cmpsource" value="<?php echo request()->get('cmpsource');?>">
                                          <input type="hidden" id="type" name="type" value="<?php echo request()->get('type');?>">
                                               <input type="hidden" id="product_namesearch" name="product_namesearch" value="<?php echo request()->get('product_namesearch');?>">
                                  
                                                                     <input type="hidden" id="employee" name="employee" value="<?php echo request()->get('employee');?>">
                          <input type="submit" class="btn btn-primary" name="export" value="Export to Excel">

                                 </form>
    </div>
</div>
     
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <h5>
                    @switch(request()->get('type'))
                        @case('resolved')
                            Resolved Complaints
                            @break
                        @case('crossedtl')
                            Complaints with Crossed Timeline
                            @break
                        @case('pending')
                            Pending Complaints
                            @break
                        @default
                            All Complaints
                    @endswitch
                    
                </h5>
            </div>
            <div class="card-block">
                <div class="dt-responsive table-responsive">
                    @php
                        $i = 1;
                    @endphp
                    <table id="simpletable" class="table table-striped table-bordered nowrap complainttable">
                        <thead>
                            <tr>

                                <th>UUID</th>
                                <th>Days In System</th>
                                <th>Complaint Source</th> 
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data as $key => $item)
                            @if (optional($item->complaint)->uuid != '')
                                
                          
                            <tr>

                                <th> <a href="/ceo/trackcomplaintceo?refno={{optional($item->complaint)->uuid}}" target="_blank" title="Track this complaint" data-toggle="tooltip" data-placement="right">{{optional($item->complaint)->uuid ?? 'N/A'}}</a></th>
                                <th>
                                    <label
                                        @if (request()->has('crossedtl'))
                                        class="label badge-danger"
                                        @else
                                        class="label badge-primary"
                                        @endif>
                                        @if (optional($item->complaint)->created_at)
                                        {{optional($item->complaint)->created_at->diffInDays(now()) ?? 'N/A'}}
                                        Days
                                        @endif
                                    </label><br>
                                    <label class="label badge-warning">Created On
                                        :{{ datefomat(optional($item->complaint)->created_at)}}</label>
                                </th>
                                <td>{{App\Models\ComplaintSource::find($item->complaint['complaintsource'])->name ?? 'N/A'}}</td>
                                @php $Resolvedd=App\Models\Resolution::where('complaint_id',$item->complaint->id)->orderBy('id', 'desc')->first(); @endphp
                                <th>
                                    <div class="animation-model">
                                        <button type="button"
                                            class="btn btn-primary btn-outline-primary waves-effect md-trigger detailsmodalbtn"
                                            data-toggle="modal" data-target="#detailsmodal" data-details="{{$item}}"

                                            @if ($item->complaint->image)
                                            data-img='<img src="{{\Storage::disk('public')->url(optional($item->complaint)->image)}}" style="max-width: 300px;max-height: 200px;"/>'  data-Resolve="{{optional($Resolvedd)->resolution}}"
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
                            </tr>
                             
                            @php
                                $i++;
                            @endphp
                            @endif
                            @endforeach
                            @if(!empty($data6))
                            @foreach ($data6 as $key => $item1)
                            @if ($item1->uuid != '')
                                
                          
                            <tr>

                                <th> <a href="/ceo/trackcomplaintceo?refno={{optional($item1->complaint)->uuid}}" target="_blank" title="Track this complaint" data-toggle="tooltip" data-placement="right">{{$item1->uuid ?? 'N/A'}}</a></th>
                                <th>
                                    <label
                                        @if (request()->has('crossedtl'))
                                        class="label badge-danger"
                                        @else
                                        class="label badge-primary"
                                        @endif>
                                        @if ($item1->created_at)
                                        {{$item1->created_at->diffInDays(now()) ?? 'N/A'}}
                                        Days
                                        @endif
                                    </label><br>
                                    <label class="label badge-warning">Created On
                                        :{{ datefomat($item1->created_at)}}</label>
                                </th>
                                <td>{{App\Models\ComplaintSource::find($item1->complaintsource)->name ?? 'N/A'}}</td>
                                 @php $Resolvedd=App\Models\Resolution::where('complaint_id',$item1->id)->orderBy('id', 'desc')->first(); @endphp
                                <th>
                                    <div class="animation-model">
                                        <button type="button"
                                            class="btn btn-primary btn-outline-primary waves-effect md-trigger detailsmodalbtn1"
                                            data-toggle="modal" data-target="#detailsmodal" data-details="{{$item}}"
                                            @if ($item1->image)

                                            data-img='<img src="{{\Storage::disk('public')->url($item1->image)}}" style="max-width: 300px;max-height: 200px;"/>' data-Resolve="{{optional($Resolvedd)->resolution}}"
                                            data-imglink="{{\Storage::disk('public')->url($item1->image)}}"
                                          @else
                                      

                                    @php $complaintattachment=App\Models\ComplaintAttachment::where('complaint_id', $item1->id)->where('media_type', '0')->orderByDesc('id')->get();
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
                            </tr>
                             
                            @php
                                $i++;
                            @endphp
                            @endif
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- details modal --}}
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

</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function(){
        $('.detailsmodalbtn').each(function(){
            $(this).click(function(){
                var details = $(this).attr('data-details');
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
        
          $('.detailsmodalbtn1').each(function(){
            $(this).click(function(){
                var details = $(this).attr('data-details');
             //// alert(details);
              
                var img = $(this).attr('data-img');
                var resolvedd = $(this).attr('data-Resolve');

                var imglink = $(this).attr('data-imglink');
                var comdet = JSON.parse(details);
                $('#custname').html(comdet['customername']);
                $('#custcontact').html(comdet['mobile']);
                $('#custimg').html(img);
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
                $('#custdetails').html(comdet['details'])
               $('#custresolve').html(resolvedd)

            });
        });
        
        
        
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

@endsection
