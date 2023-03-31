<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use Illuminate\Http\Response;

class UserDocumentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDocumentRequest $request): Response
    {
        $user = auth()->user()->user;

        $user->update([
            'documents' => $user->documents
                ? $user->documents->merge($request->documents)
                : $request->documents
        ]);

        return response([
            'data' => $user,
            'message' => __('documents has been added successfully')
        ]);
    }

}
