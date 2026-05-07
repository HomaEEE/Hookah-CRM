<?php

namespace Modules\Dev\Filament\Admin\Clusters\Dev\Pages;

use Filament\Pages\Page;
use Modules\Dev\Filament\Admin\Clusters\Dev\DevCluster;

class Dev extends Page
{
    protected string $view = 'filament.admin.clusters.dev.pages.dev';

    protected static ?string $cluster = DevCluster::class;
}
