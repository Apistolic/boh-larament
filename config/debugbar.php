<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     |
     | Debugbar is enabled by default, when debug is set to true in app.php.
     | You can override the value by setting enable to true or false instead of null.
     |
     */
    'enabled' => false,

    /*
     |--------------------------------------------------------------------------
     | Storage settings
     |--------------------------------------------------------------------------
     |
     | DebugBar stores data for session/ajax requests.
     | You can disable this, so the debugbar stores data in headers/session,
     | but this can cause problems with large data collectors.
     | By default, file storage (in the storage folder) is used. Redis and PDO
     | can also be used. For PDO, run the package migrations first.
     |
     */
    'storage' => [
        'enabled'    => false,
        'driver'     => 'file',
        'path'       => storage_path('debugbar'),
        'connection' => null,
        'provider'   => '',
    ],

    /*
     |--------------------------------------------------------------------------
     | Editor
     |--------------------------------------------------------------------------
     |
     | Choose your preferred editor to use when clicking file name.
     |
     | Supported: "phpstorm", "vscode", "vscode-insiders", "vscode-remote",
     |            "vscode-insiders-remote", "vscodium", "textmate", "emacs",
     |            "sublime", "atom", "nova", "macvim", "idea", "netbeans",
     |            "xdebug"
     |
     */
    'editor' => 'vscode',

    /*
     |--------------------------------------------------------------------------
     | Remote Path Mapping
     |--------------------------------------------------------------------------
     |
     | If you are using a remote dev server, like Laravel Homestead, Docker, or
     | even a remote VPS, it will be necessary to specify your path mapping.
     |
     | Leaving one, or both of these, empty or null will not trigger the remote
     | URL changes and Debugbar will treat your editor links as local files.
     |
     | "remote_sites_path" is an absolute base path for your sites or projects
     | in Homestead, Vagrant, Docker, or another remote development server.
     |
     | Example value: "/home/vagrant/Code"
     |
     | "local_sites_path" is an absolute base path for your sites or projects
     | on your local computer where your IDE or code editor is running on.
     |
     | Example values: "/Users/<name>/Code", "C:\Users\<name>\Documents\Code"
     |
     */
    'remote_sites_path' => null,
    'local_sites_path' => null,
];
