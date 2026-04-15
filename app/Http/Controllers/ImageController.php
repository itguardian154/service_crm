<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Rotate image
     */
    public function rotate(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'direction' => 'required|in:left,right',
        ]);
        

        $result = $this->imageService->rotateExistingImage(
            $request->path,
            $request->direction
        );

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => 'Image rotated successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to rotate image',
        ], 400);
    }
}