<?php

use App\Providers\AppServiceProvider;
use App\Providers\Xelqun\AdminPanelProvider;
use App\Providers\Xelqun\WorkspacePanelProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    WorkspacePanelProvider::class,
];
