<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ShortenedUrl;
use Illuminate\Support\Str;

class ShortenUrlController extends Controller
{
    /**
     * Create a shortened URL
     */
    public function shortenUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $existingUrl = ShortenedUrl::where('user_id', $request->user()->id)
            ->where('original_url', $request->original_url)
            ->first();

        if ($existingUrl) {
            return response()->json([
                'success' => false,
                'message' => 'This URL has already been shortened',
                'data' => [
                    'short_code' => $existingUrl->short_code,
                    'short_url' => route('shortened.redirect', $existingUrl->short_code),
                ]
            ], 409);
        }

        $shortCode = $this->generateUniqueShortCode();

        $shortenedUrl = ShortenedUrl::create([
            'user_id' => $request->user()->id,
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'URL shortened successfully',
            'data' => [
                'id' => $shortenedUrl->id,
                'original_url' => $shortenedUrl->original_url,
                'short_code' => $shortenedUrl->short_code,
                'short_url' => route('shortened.redirect', $shortenedUrl->short_code),
                'created_at' => $shortenedUrl->created_at,
            ]
        ], 201);
    }

    /**
     * Get all shortened URLs for authenticated user
     */
    public function getUserUrls(Request $request)
    {
        $urls = ShortenedUrl::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($url) {
                return [
                    'id' => $url->id,
                    'original_url' => $url->original_url,
                    'short_code' => $url->short_code,
                    'short_url' => route('shortened.redirect', $url->short_code),
                    'created_at' => $url->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'URLs retrieved successfully',
            'data' => $urls,
            'total' => $urls->count()
        ], 200);
    }

    /**
     * Delete a shortened URL
     */
    public function deleteUrl(Request $request, $id)
    {
        $shortenedUrl = ShortenedUrl::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$shortenedUrl) {
            return response()->json([
                'success' => false,
                'message' => 'URL not found'
            ], 404);
        }

        $shortenedUrl->delete();

        return response()->json([
            'success' => true,
            'message' => 'URL deleted successfully'
        ], 200);
    }

    /**
     * Generate unique short code
     */
    private function generateUniqueShortCode()
    {
        do {
            $shortCode = Str::random(6);
        } while (ShortenedUrl::where('short_code', $shortCode)->exists());

        return $shortCode;
    }
}
