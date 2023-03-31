<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Response;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(UploadFileRequest $request): Response
    {
        $file = File::create([
            'name' => FileService::upload($request->file, $request->type),
            'folder' => $request->type
        ]);

        return response(['data' => $file->name], 201);
    }
}
