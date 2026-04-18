module.exports = {
    apps: [
        {
            name: "laravel-echo-server",
            script: "laravel-echo-server",
            args: "start",
            cwd: "/var/www/html",
            exec_mode: "fork",
            instances: 1,
            autorestart: true,
            out_file: "/dev/null",
            error_file: "/dev/null",
            vizion: false,
            pmx: false
        }
    ]
};