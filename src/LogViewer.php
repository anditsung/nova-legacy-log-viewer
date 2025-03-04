<?php

namespace Anditsung\NovaLegacyLogViewer;

use Illuminate\Support\Facades\File as FileFacade;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;
use Symfony\Component\Finder\SplFileInfo;

class LogViewer extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('nova-legacy-log-viewer', __DIR__.'/../dist/js/tool.js');
        Nova::style('nova-legacy-log-viewer', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return view('nova-legacy-log-viewer::navigation');
    }

    public static function logs()
    {
        return collect(FileFacade::allFiles(storage_path('logs')))
            ->filter(fn (SplFileInfo $log) => $log->getExtension() === 'log')
            ->map(function (SplFileInfo $log) {
                return [
                    'label' => $log->getRelativePathname(),
                    'value' => $log->getRelativePathname(),
                ];
            })
            ->sortByDesc('label')
            ->values();
    }
}
