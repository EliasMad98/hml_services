{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
 --}}
 <!DOCTYPE html>
 <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
     <head>
         <meta charset="utf-8">
         <meta name="viewport" content="width=device-width, initial-scale=1">

         <title>Chat App Socket.io</title>

         <!-- Fonts -->
         <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
         <!-- CSS only -->
         <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
         <style>
             .chat-row {
                 margin: 50px;
             }

              ul {
                  margin: 0;
                  padding: 0;
                  list-style: none;
              }

              ul li {
                  padding:8px;
                  background: #928787;
                  margin-bottom:20px;
              }

              ul li:nth-child(2n-2) {
                 background: #c3c5c5;
              }

              .chat-input {
                  border: 1px soild lightgray;
                  border-top-right-radius: 10px;
                  border-top-left-radius: 10px;
                  padding: 8px 10px;
                  color:#fff;
              }
         </style>
     </head>
     <body>

         {{-- <div class="container">
             <div class="row chat-row">
                 <div class="chat-content">
                     <ul>

                     </ul>
                 </div>

                 <div class="chat-section">
                     <div class="chat-box">
                         <div class="chat-input bg-primary" id="toSend" contenteditable="">

                         </div>
                     </div>
                 </div>
             </div>
         </div> --}}
         <form action="/sendmessage" method="post">
            @csrf
            <h1>ooo</h1>
            {{-- <input type="hidden" name="user" value="{{ Auth::user()->name }}" > --}}
    <input name="body" type="text" class="form-control" placeholder="Say something and hit the Enter" id="message" >
    </form>

         <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
         <script src="https://cdn.socket.io/4.0.1/socket.io.min.js" integrity="sha384-LzhRnpGmQP+lOvWruF/lgkcqD+WDVt9fU3H4BWmwP5u5LTmkUGafMcpZKNObVMLU" crossorigin="anonymous"></script>

         <script>
             $(function() {

                 let ip_address = 'https://chat.arabesquegallery.ae';
                 let socket_port = '3000';
 // Connect to Socket.io
//  io.on('connection', (socket) => {
//     console.log('hiiii');

//              });


                 let socket = io(ip_address + ':' + socket_port);
                 socket.on('welcome', data => {

                  console.log(data);

                                });
                 socket.on('private-2-1:App\\Events\\MessageSent', data => {

                    console.log(data);

        });
                //  socket.on('private-2-1', (data) => {
                //     console.log(data);
                //      $('.chat-content ul').append(`<li>${data}</li>`);
                //  });


                //  let chatInput = $('#chatInput');
                 let toSend = $('#toSend');

                 $('#toSend').keypress(function(e) {
        if(e.which==13) {
            e.preventDefault();
            let text = $('#toSend').val();
            let message = $(this).html();
                     console.log(message);
                        //  socket.emit('sendChatToServer', message);
                         toSend.html('');
                         return false;
            $.ajax({
                url: "/sendmessage",
                method:"post",
                data : {
                    body : text,
                    _token:"{{ csrf_token() }}"
                }
              }).done(function(response) {
                  console.log(response)
                  if(response=='OK') {
                    $('#toSend').val('');
                  }
              }).fail(function(r) {
                  console.log(r)
              });


        }
    })
                //  chatInput.keypress(function(e) {
                //      let message = $(this).html();
                //      console.log(message);
                //      if(e.which === 13 && !e.shiftKey) {
                //          socket.emit('sendChatToServer', message);
                //          chatInput.html('');
                //          return false;
                //      }
                //  });

                //  socket.on('sendChatToClient', (message) => {
                //      $('.chat-content ul').append(`<li>${message}</li>`);
                //  });
             });
         </script>
     </body>
 </html>

