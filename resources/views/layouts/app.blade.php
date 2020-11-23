<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style type="text/css">
        ul
        {
            margin: 0;
            padding: 0;
        }

        li
        {
            list-style: none;
        }

        .user-wrapper .users, 
        .message-wrapper .messages
        {
            height: 530px;
            overflow-y: auto;
        }

        .user
        {
            cursor: pointer;
            padding: 5px 0;
            position: relative;
        }

        .user:hover
        {
            background: #eeeeee;
        }

        .user:last-child {
            margin: 0;
        }

        .pending
        {
            position: absolute;
            left: 13px;
            top: 9px;
            background: #b600ff;
            margin: 0;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            line-height: 16px;
            padding-left: 4px;
            color: #ffffff;
            font-size: 12px;
        }

        .media-left
        {
            margin: 0 10px;
        }

        .media-left img {
            width: 64px;
            border-radius: 64px;
        }

        .media-body p
        {
            padding: 6px 0;
        }

        .message-wrapper
        {
            padding: 10px;
            background: #eeeeee;
            border: 1px solid #dddddd;
        }

        .messages .message
        {
            margin-bottom: 15px;
        }

        .messages .message:last-child
        {
            margin-bottom: 0;
        }  

        .received,
        .sent
        {
            width: 45%;
            padding: 3px 10px;
            border-radius: 10px;
        }      

        .received {
            background: #ffffff;
        }

        .sent
        {
            background: #3bebff;
            float: right;
            text-align: right;
        }

        .message p
        {
            margin: 5px 0;
        }

        .date
        {
            color: #777777;
            font-size: 12px;
        }

        .active
        {
            background: #eeeeee;
        }

        input[type="text"]
        {
            width: 100%;
            padding: 12px 20px;
            margin: 15px 0 0 0;
            display: inline-block;
            border-radius: 4px;
            box-sizing: border-box;
            outline: none;
            border: 1px solid #eeeeee;
        }

        input[type="text"]:focus
        {
            border: 1px soldi #aaaaaa;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
   <script type="text/javascript">
        var receiver_id = '';
        var my_id       = {{ auth()->user()->id ?? '' }}
       $(document).ready(function (event) {
            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content');
            //     }
            // });

            // Enable pusher logging - don't include this in production
            // Pusher.logToConsole = true;

            var pusher = new Pusher('a14cb49351893892dcfd', {
              cluster: 'eu'
            });

            var channel = pusher.subscribe('my-channel');
            channel.bind('my-event', function(data) {
              if (my_id == data.from) {
                $("#" + data.to).click();
              } else if(my_id == data.to) {
                if (receiver_id == data.from) {
                    // if receiver is selected reload the selected user
                    $('#'+data.from).click()
                } else {
                    // If receiver is not selected, add notification for that user
                    var pending = '';
                    if (pending) {
                        $("#" + data.from).find('.pending').html(pending + 1);
                    } else {
                        $("#" + data.from).append('<span class="pending">1</span>');
                    }
                }
              }
            });

           $('.user').on('click', function (event) {
               $('.user').removeClass('active');
               $(this).addClass('active');

               receiver_id = $(this).attr('id');

               $.ajax({
                type: 'get',
                url: '/message/'+receiver_id,
                data: '',
                cache: false,
                success: function (data) {
                    $('#messages').html(data);
                    scrollToBottom();
                }
               })
           });

           $(document).on('keyup', '.input-text input', function (event) {
               var message = $(this).val();

               if (event.keyCode == 13 && message != '' && receiver_id != '')
               {
                    $(this).val('');
                    var datastr = "receiver_id" + receiver_id + "&message=" + message;
                    $.ajax({
                        type: 'post',
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        url: 'message',
                        data: {
                            receiver_id: receiver_id,
                            message: message
                        },
                        cache: false,
                        success: function (data) {
                            
                        },
                        error: function (jqXHR, status, err) {
                            // body...
                        },
                        complete: function () {
                            // body...
                        }
                    });
               }
           });
       });

       function scrollToBottom() {
           $('.message-wrapper').animate({
            scrollTop: $('.message-wrapper').get(0).scrollHeight
           }, 50);
       }
   </script>
</body>
</html>
