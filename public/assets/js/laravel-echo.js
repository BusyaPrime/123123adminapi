import Echo from "laravel-echo"
console.log(Echo);
window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: window.location.hostname + ':6002'
});