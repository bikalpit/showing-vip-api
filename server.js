require("dotenv").config();
var dateFormat = require('dateformat');
var mysql = require("mysql");
var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http, {
    cors: {
        origin: '*'
    }
});

var connection = mysql.createConnection({
    port: process.env.DB_PORT,
    host: process.env.DB_HOST,
    user: process.env.DB_USERNAME,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_DATABASE,
    connectionLimit: 100
});
 
connection.connect(function (error) {
    if (error) {
        return console.log(error);
    }
    console.log('Connected Successfully');
});

var users = [];

http.listen(8005, function () {
    console.log('Listening to port 8005');
});

io.on('connection', function (socket) {
    socket.on('user_connected', function (user_id){
        users[user_id] = socket.id;
        io.emit('updateuserStatus', users);
        console.log('user connected' + user_id);
    });

    socket.on('message', function (data){
        console.log("receiver :" + data.receiver_id);
        console.log("sender :" + data.sender_id);
        console.log("message :" + data.message);
        var day = dateFormat(new Date(), "yyyy-mm-dd h:MM:ss");
        io.to(`${users[data.receiver_id]}`).emit('message', data);
        connection.query(
            `insert into messages(sender_id, receiver_id, message, created_at) 
                      values(?,?,?,?)`,
            [
              data.sender_id,
              data.receiver_id,
              data.message,
              day
            ],
            (error) => {
              if (error) {
                console.log(error);
              }
            }
          );
    });

    socket.on('disconnect', function(){
        var i = users.indexOf(socket.id);
        users.slice(i,1,0);
        io.emit('updateuserStatus', users);
        console.log(users);
    });
});