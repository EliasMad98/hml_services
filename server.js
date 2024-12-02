const express = require('express');
const app = express();

const fs = require('node:fs');

const options = {
  key: fs.readFileSync('../ssl/keys/de35a_34a95_5e6a7acb9db0cb94bc5a7abf67bc8582.key'),
  cert: fs.readFileSync('../ssl/certs/api_hml_uae_com_de35a_34a95_1708127999_4221aac9285818cac536d7874561a9d7.crt'),
    passphrase: ''
};

 //Requring the ioredis package

//  const server = require('https').createServer(options,app);
  const server = require('https').createServer(options,app);

 //Requring the ioredis package


var Redis = require('ioredis');

//A redis client
var redis = new Redis();
redis.setMaxListeners(20);
// Create a new Socket.io instance

const io = require('socket.io')(server, {
    cors: { origin: "*"}
});
io.setMaxListeners(20); 

// redis.psubscribe('*');
// console.log('kiii');


/*io.on('connection', function(socket) {
    socket.on('room', function(room){
        socket.join(room);
    });

    socket.on('disconnect', function(){
      console.log("disconnected!");
    });
});

redis_client.on('message', function(channel, message) {
    var myData = JSON.parse(message);
      io.to('17').emit('cacad', 'u i u a a');
  });*/
io.on('connection', (socket) => {

   //  socket.on('connection',function(io) {
        io.emit('welcome','You are now successfully connected to the socket .');
        console.log(`a connection has been made . ${socket.id}`);
// Subscribe to all channels which name complies with the '*' pattern
// '*' means we'll subscribe to ALL possible channels

        redis.psubscribe('*');
        // console.log('kiii');
// Listen for new messages




    socket.on('disconnect', (socket) => {
        console.log('Disconnect');
    });
});

        redis.on('pmessage',function(pattern, channel, message) {
        //  console.log('kiii');
        //  console.log(channel);
        message = JSON.parse(message);
        // console.log(message.data.channel);

        io.emit(message.data.channel  ,  message);
        // console.log(channel + ':' + message.event, message.data);

        })
// Start the server at http://localhost:3000

server.listen(3000, () => {
    console.log('Server is running');
});



