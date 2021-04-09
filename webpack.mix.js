const mix = require("laravel-mix");

mix.options({
    extractVueStyles: false,
    postCss: [
        {
            purge: [],
        },
    ],
});

mix
    .js("js/2fa.js", "dist").vue({ version: 2 })
    .js("js/2fa-login.js", "dist").vue({ version: 2 })
    .disableNotifications();
