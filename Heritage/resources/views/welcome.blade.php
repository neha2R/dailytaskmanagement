<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Heritage</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
    <?php echo csrf_token(); ?>
       <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>
              <script src="https://cdn.socket.io/4.4.1/socket.io.min.js"></script>

               <script type="text/javascript">
 //var to_user_id ={{ auth()->id() }}
 //let channelId='App.Models.User.${Echo.socketId()}';
///Echo.private('App.Models.User.${Echo.socketId()}')
////const io = require('socket.io-client');


const socket = io('wss://cultre.app:6002');

socket.on('connect', () => {
  console.log('Connected to the server!');
});

socket.on('your-event-name', (data) => {
  console.log('Received data:', data);
});


 // Echo.channel('events')
 //  .listen('RealTimeMessage', (e) => console.log('RealTimeMessage: ' + e.message));
//Echo.private('App.Models.User.1')
     // .notification((notification) => {
         //   console.log(notification.message);
    // });
    
     </script>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <!-- <a href="{{ route('register') }}">Register</a> -->
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                Heritage
                </div>


            </div>
        </div>
    </body>
</html>
