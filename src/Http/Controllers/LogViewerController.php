<?php

namespace Anditsung\NovaLegacyLogViewer\Http\Controllers;

use Anditsung\NovaLegacyLogViewer\LogViewer;
use Exception;
use Illuminate\Http\Request;
use Anditsung\NovaLegacyLogViewer\File;

class LogViewerController
{
    public function __invoke()
    {
        return LogViewer::logs();
    }

    public function fetch(Request $request)
    {
        if (! str_starts_with(realpath(storage_path('logs/'.$request->log)), realpath(storage_path()))) {
            throw new Exception('Invalid log path.');
        }

        $request->validate(['lastLine' => ['numeric']]);
        $logFile = new File(storage_path('logs/'.$request->log));
        $lines = $logFile->contentAfterLine($request->lastLine);
        $lastLine = $request->lastLine + substr_count($lines, PHP_EOL);

        return response()->json([
            'lastLine' => $lastLine,
            'content' => $lines,
            'numberOfLines' => $logFile->numberOfLines(),
        ]);
    }

    public function delete(Request $request)
    {
        unlink(storage_path('logs/'.$request->log));
    }
}