<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Track Customer Complaint</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<!------ Include the above in your HEAD tag ---------->
<style>
    @import url("https://fonts.googleapis.com/css?family=Rubik:500,700|Roboto:400,600");

    .section-padding {
        padding: 45px 0;
    }

    .section-dark {
        background-color: #f9f9f9;
        z-index: -2;
    }

    .form-control,
    .form-group .form-control {
        border: 0;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#009688), to(#009688)), -webkit-gradient(linear, left top, left bottom, from(#D2D2D2), to(#D2D2D2));
        background-image: -webkit-linear-gradient(#009688, #009688), -webkit-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: -o-linear-gradient(#009688, #009688), -o-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: linear-gradient(#009688, #009688), linear-gradient(#D2D2D2, #D2D2D2);
        -webkit-background-size: 0 2px, 100% 1px;
        background-size: 0 2px, 100% 1px;
        background-repeat: no-repeat;
        background-position: center bottom, center -webkit-calc(100% - 1px);
        background-position: center bottom, center calc(100% - 1px);
        background-color: rgba(0, 0, 0, 0);
        -webkit-transition: background 0s ease-out;
        -o-transition: background 0s ease-out;
        transition: background 0s ease-out;
        float: none;
        -webkit-box-shadow: none;
        box-shadow: none;
        border-radius: 0
    }

    .form-control::-moz-placeholder,
    .form-group .form-control::-moz-placeholder {
        color: #BDBDBD;
        font-weight: 400
    }

    .form-control:-ms-input-placeholder,
    .form-group .form-control:-ms-input-placeholder {
        color: #BDBDBD;
        font-weight: 400
    }

    .form-control::-webkit-input-placeholder,
    .form-group .form-control::-webkit-input-placeholder {
        color: #BDBDBD;
        font-weight: 400
    }

    .form-control[disabled],
    .form-control[readonly],
    .form-group .form-control[disabled],
    .form-group .form-control[readonly],
    fieldset[disabled] .form-control,
    fieldset[disabled] .form-group .form-control {
        background-color: rgba(0, 0, 0, 0)
    }

    .form-control[disabled],
    .form-group .form-control[disabled],
    fieldset[disabled] .form-control,
    fieldset[disabled] .form-group .form-control {
        background-image: none;
        border-bottom: 1px dotted #D2D2D2
    }

    .form-group {
        position: relative
    }

    .form-group.label-floating label.control-label,
    .form-group.label-placeholder label.control-label,
    .form-group.label-static label.control-label {
        position: absolute;
        pointer-events: none;
        -webkit-transition: .3s ease all;
        -o-transition: .3s ease all;
        transition: .3s ease all
    }

    .form-group.label-floating label.control-label {
        will-change: left, top, contents
    }

    .form-group.label-placeholder:not(.is-empty) label.control-label {
        display: none
    }

    .form-group .help-block {
        position: absolute;

    }

    .form-group.is-focused .form-control {
        outline: 0;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#009688), to(#009688)), -webkit-gradient(linear, left top, left bottom, from(#D2D2D2), to(#D2D2D2));
        background-image: -webkit-linear-gradient(#009688, #009688), -webkit-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: -o-linear-gradient(#009688, #009688), -o-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: linear-gradient(#009688, #009688), linear-gradient(#D2D2D2, #D2D2D2);
        -webkit-background-size: 100% 2px, 100% 1px;
        background-size: 100% 2px, 100% 1px;
        -webkit-box-shadow: none;
        box-shadow: none;
        -webkit-transition-duration: .3s;
        -o-transition-duration: .3s;
        transition-duration: .3s
    }

    .form-group.is-focused .form-control .material-input:after {
        background-color: #009688
    }

    .form-group.is-focused label,
    .form-group.is-focused label.control-label {
        color: #009688
    }

    .form-group.is-focused.label-placeholder label,
    .form-group.is-focused.label-placeholder label.control-label {
        color: #BDBDBD
    }

    .form-group.is-focused .help-block {
        display: block
    }

    .form-group.has-warning .form-control {
        -webkit-box-shadow: none;
        box-shadow: none
    }

    .form-group.has-warning.is-focused .form-control {
        background-image: -webkit-gradient(linear, left top, left bottom, from(#ff5722), to(#ff5722)), -webkit-gradient(linear, left top, left bottom, from(#D2D2D2), to(#D2D2D2));
        background-image: -webkit-linear-gradient(#ff5722, #ff5722), -webkit-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: -o-linear-gradient(#ff5722, #ff5722), -o-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: linear-gradient(#ff5722, #ff5722), linear-gradient(#D2D2D2, #D2D2D2)
    }

    .form-group.has-warning .help-block,
    .form-group.has-warning label.control-label {
        color: #ff5722
    }

    .form-group.has-error .form-control {
        -webkit-box-shadow: none;
        box-shadow: none
    }

    .form-group.has-error .help-block,
    .form-group.has-error label.control-label {
        color: #f44336
    }

    .form-group.has-success .form-control {
        -webkit-box-shadow: none;
        box-shadow: none
    }

    .form-group.has-success.is-focused .form-control {
        background-image: -webkit-gradient(linear, left top, left bottom, from(#4caf50), to(#4caf50)), -webkit-gradient(linear, left top, left bottom, from(#D2D2D2), to(#D2D2D2));
        background-image: -webkit-linear-gradient(#4caf50, #4caf50), -webkit-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: -o-linear-gradient(#4caf50, #4caf50), -o-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: linear-gradient(#4caf50, #4caf50), linear-gradient(#D2D2D2, #D2D2D2)
    }

    .form-group.has-success .help-block,
    .form-group.has-success label.control-label {
        color: #4caf50
    }

    .form-group.has-info .form-control {
        -webkit-box-shadow: none;
        box-shadow: none
    }

    .form-group.has-info.is-focused .form-control {
        background-image: -webkit-gradient(linear, left top, left bottom, from(#03a9f4), to(#03a9f4)), -webkit-gradient(linear, left top, left bottom, from(#D2D2D2), to(#D2D2D2));
        background-image: -webkit-linear-gradient(#03a9f4, #03a9f4), -webkit-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: -o-linear-gradient(#03a9f4, #03a9f4), -o-linear-gradient(#D2D2D2, #D2D2D2);
        background-image: linear-gradient(#03a9f4, #03a9f4), linear-gradient(#D2D2D2, #D2D2D2)
    }

    .form-group.has-info .help-block,
    .form-group.has-info label.control-label {
        color: #03a9f4
    }

    .form-group textarea {
        resize: none
    }

    .form-group textarea~.form-control-highlight {
        margin-top: -11px
    }

    .form-group select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none
    }

    .form-group select~.material-input:after {
        display: none
    }

    .form-control {
        margin-bottom: 7px
    }

    .form-control::-moz-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-control:-ms-input-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-control::-webkit-input-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .checkbox label,
    .radio label,
    label {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    label.control-label {
        font-size: 12px;
        line-height: 1.07142857;
        font-weight: 400;
        margin: 16px 0 0 0
    }

    .help-block {
        margin-top: 0;
        font-size: 12px
    }

    .form-group {
        padding-bottom: 25px;
        margin: 28px 0 0 0
    }

    .form-group .form-control {
        margin-bottom: 7px
    }

    .form-group .form-control::-moz-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group .form-control:-ms-input-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group .form-control::-webkit-input-placeholder {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group .checkbox label,
    .form-group .radio label,
    .form-group label {
        font-size: 16px;
        line-height: 1.42857143;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group label.control-label {
        font-size: 12px;
        line-height: 1.07142857;
        font-weight: 400;
        margin: 16px 0 0 0
    }

    .form-group .help-block {
        margin-top: 0;
        font-size: 12px
    }

    .form-group.label-floating label.control-label,
    .form-group.label-placeholder label.control-label {
        top: -7px;
        font-size: 16px;
        line-height: 1.42857143
    }

    .form-group.label-floating.is-focused label.control-label,
    .form-group.label-floating:not(.is-empty) label.control-label,
    .form-group.label-static label.control-label {
        top: -30px;
        left: 0;
        font-size: 12px;
        line-height: 1.07142857
    }

    .form-group.label-floating input.form-control:-webkit-autofill~label.control-label label.control-label {
        top: -30px;
        left: 0;
        font-size: 12px;
        line-height: 1.07142857
    }

    .form-group.form-group-sm {
        padding-bottom: 3px;
        margin: 21px 0 0 0
    }

    .form-group.form-group-sm .form-control {
        margin-bottom: 3px
    }

    .form-group.form-group-sm .form-control::-moz-placeholder {
        font-size: 11px;
        line-height: 1.5;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-sm .form-control:-ms-input-placeholder {
        font-size: 11px;
        line-height: 1.5;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-sm .form-control::-webkit-input-placeholder {
        font-size: 11px;
        line-height: 1.5;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-sm .checkbox label,
    .form-group.form-group-sm .radio label,
    .form-group.form-group-sm label {
        font-size: 11px;
        line-height: 1.5;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-sm label.control-label {
        font-size: 9px;
        line-height: 1.125;
        font-weight: 400;
        margin: 16px 0 0 0
    }

    .form-group.form-group-sm .help-block {
        margin-top: 0;
        font-size: 9px
    }

    .form-group.form-group-sm.label-floating label.control-label,
    .form-group.form-group-sm.label-placeholder label.control-label {
        top: -11px;
        font-size: 11px;
        line-height: 1.5
    }

    .form-group.form-group-sm.label-floating.is-focused label.control-label,
    .form-group.form-group-sm.label-floating:not(.is-empty) label.control-label,
    .form-group.form-group-sm.label-static label.control-label {
        top: -25px;
        left: 0;
        font-size: 9px;
        line-height: 1.125
    }

    .form-group.form-group-sm.label-floating input.form-control:-webkit-autofill~label.control-label label.control-label {
        top: -25px;
        left: 0;
        font-size: 9px;
        line-height: 1.125
    }

    .form-group.form-group-lg {
        padding-bottom: 9px;
        margin: 30px 0 0 0
    }

    .form-group.form-group-lg .form-control {
        margin-bottom: 9px
    }

    .form-group.form-group-lg .form-control::-moz-placeholder {
        font-size: 18px;
        line-height: 1.3333333;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-lg .form-control:-ms-input-placeholder {
        font-size: 18px;
        line-height: 1.3333333;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-lg .form-control::-webkit-input-placeholder {
        font-size: 18px;
        line-height: 1.3333333;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-lg .checkbox label,
    .form-group.form-group-lg .radio label,
    .form-group.form-group-lg label {
        font-size: 18px;
        line-height: 1.3333333;
        color: #BDBDBD;
        font-weight: 400
    }

    .form-group.form-group-lg label.control-label {
        font-size: 14px;
        line-height: .99999998;
        font-weight: 400;
        margin: 16px 0 0 0
    }

    .form-group.form-group-lg .help-block {
        margin-top: 0;
        font-size: 14px
    }

    .form-group.form-group-lg.label-floating label.control-label,
    .form-group.form-group-lg.label-placeholder label.control-label {
        top: -5px;
        font-size: 18px;
        line-height: 1.3333333
    }

    .form-group.form-group-lg.label-floating.is-focused label.control-label,
    .form-group.form-group-lg.label-floating:not(.is-empty) label.control-label,
    .form-group.form-group-lg.label-static label.control-label {
        top: -32px;
        left: 0;
        font-size: 14px;
        line-height: .99999998
    }

    .form-group.form-group-lg.label-floating input.form-control:-webkit-autofill~label.control-label label.control-label {
        top: -32px;
        left: 0;
        font-size: 14px;
        line-height: .99999998
    }

    select.form-control {
        border: 0;
        -webkit-box-shadow: none;
        box-shadow: none;
        border-radius: 0
    }

    .form-group.is-focused select.form-control {
        -webkit-box-shadow: none;
        box-shadow: none;
        border-color: #D2D2D2
    }

    .form-group.is-focused select.form-control[multiple],
    select.form-control[multiple] {
        height: 85px
    }

    .input-group-btn .btn {
        margin: 0 0 7px 0
    }

    .form-group.form-group-sm .input-group-btn .btn {
        margin: 0 0 3px 0
    }

    .form-group.form-group-lg .input-group-btn .btn {
        margin: 0 0 9px 0
    }

    .input-group .input-group-btn {
        padding: 0 12px
    }

    .input-group .input-group-addon {
        border: 0;
        background: 0 0
    }

    .form-group input[type=file] {
        opacity: 0;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 100
    }

    .contact-widget-section .single-contact-widget {
        background: #f9f9f9;
        padding: 20px 25px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.26);
        height: 260px;
        margin-top: 25px;
        transition: all 0.3s ease-in-out
    }

    .contact-widget-section .single-contact-widget i {
        font-size: 75px
    }

    .contact-widget-section .single-contact-widget h3 {
        font-size: 20px;
        color: #333;
        font-weight: 700;
        padding-bottom: 10px
    }

    .contact-widget-section .single-contact-widget p {
        line-height: 16px
    }

    .contact-widget-section .single-contact-widget:hover {
        background: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.46);
        cursor: pointer;
        transition: all 0.3s ease-in-out
    }

    #contactForm {
        margin-top: -10px
    }

    #contactForm .form-group label.control-label {
        color: #000;
        font-size: 17px;
    }

    #contactForm .form-control {
        font-weight: 500;
        height: auto
    }

    .section-title {
        color: #fff;
        margin-top: 25px;
    }

    .imglogo {
        width: 120px;
        height: 74px;
    }

    .btn-common {
        padding: 10px 50px !important;
        background: #ee272c !important;
        border-radius: 30px !important;
        color: #fff;
    }
</style>
</head>
<body>
    <!-- Contact Us Section -->
<div style="background: rgb(236, 32, 40);">
    <div class="container">
        <div class="row">
            <div class="col-md-4"><a class="navbar-brand" href="https://www.bikaji.com"><img src="{{URL::asset('logo1.png')}}"
                        class="imglogo"></a></div>
            <div class="col-md-8">
                <h3 class="section-title">Track your complaint here</h3>
            </div>
        </div>
    </div>
</div>
<section class="Material-contact-section section-padding section-dark">


@if (request()->has('mobileno'))
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-block">
                    <div style="overflow-x: scroll">
                        <table id="simpletable" class="table table-striped table-bordered nowrap">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    {{-- <th>UUID</th> --}}
                                    {{-- <th>Customer Name</th> --}}
                                    <th>Title</th>
                                    <th>Details</th>
                                    <th>Tracking Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($getcomplaints))
                                @foreach ($getcomplaints as $key => $item)
                                <tr>
                                    <th>{{$key + 1}}</th>
                                    {{-- <th>{{$item->uuid ?? 'N/A'}}</th> --}}
                                    {{-- <th>{{$item->customername ?? 'N/A'}}</th> --}}
                                    <th>{{$item->title ?? 'N/A'}}</th>
                                    <th>{{$item->details ?? 'N/A'}}</th>
                                    <td><a href="?refno={{$item->uuid}}" class="btn btn-default">Track</a></td>
                                </tr>
                                @endforeach
                                @else
                                    <tr>
                                        <th colspan="4" style="text-align: center;"> No complaints are registered with this number </th>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="container">
    <div class="row">
          <!-- Section Titile -->
          <div class="col-md-12 wow animated fadeInLeft" data-wow-delay=".2s" style="text-align: center">
              
              <p style="margin-bottom: 40px;">Enter your mobile number to track your complaints.</p>
          </div>
      </div>
    <div class="row">
        <div class="col-md-12 wow animated fadeInRight" data-wow-delay=".2s" style="text-align: center">
            @if (session()->has('msg'))
            <div class="alert alert-success" id="msg">
                {{session()->get('msg')}}
            </div>
            @endif
            @if (session()->has('message'))
            <div class="alert alert-danger" id="message">
                {{session()->get('message')}}
            </div>
            @endif
            <form class="form-horizontal" method="GET" id="contactForm" name="contact-form">
                @csrf
                <div class="form-group label-floating">
                    <label class="control-label" for="mobileno">Mobile Number</label>
                    <input class="form-control" id="mobileno" type="text" name="mobileno" required>
                </div>
                  <!-- Form Submit -->
                <div class="form-submit mt-5">
                    <button class="btn btn-common" type="submit" id="form-submit"><i
                            class="material-icons mdi mdi-message-outline"></i> Submit</button>
                    <div id="msgSubmit" class="h3 text-center hidden"></div>
                    <div class="clearfix"></div>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
</section>
</body>
</html>