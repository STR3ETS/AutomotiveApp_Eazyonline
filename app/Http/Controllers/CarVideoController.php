<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarVideo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CarVideoController extends Controller
{
    /**
     * Toon alle videos voor een auto
     */
    public function index(Car $car): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        $videos = $car->videos()->with('company')->get();

        return response()->json([
            'success' => true,
            'videos' => $videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'video_url' => $video->video_url,
                    'embed_url' => $video->embed_url,
                    'platform' => $video->platform,
                    'platform_video_id' => $video->platform_video_id,
                    'video_title' => $video->video_title,
                    'video_description' => $video->video_description,
                    'category' => $video->category,
                    'duration' => $video->duration,
                    'thumbnail_url' => $video->thumbnail_url,
                    'is_featured' => $video->is_featured,
                    'sort_order' => $video->sort_order,
                    'created_at' => $video->created_at->format('d-m-Y H:i'),
                ];
            })
        ]);
    }

    /**
     * Voeg nieuwe video toe
     */
    public function store(Request $request, Car $car): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'video_url' => 'required|url',
            'video_title' => 'nullable|string|max:255',
            'video_description' => 'nullable|string|max:1000',
            'category' => 'nullable|in:exterior,interior,engine,test_drive,overview,other',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatiefout',
                'errors' => $validator->errors()
            ], 422);
        }

        // Bepaal platform op basis van URL
        $platform = $this->detectPlatform($request->video_url);

        // Haal sort_order op als niet opgegeven
        $sortOrder = $request->sort_order ?? $this->getNextSortOrder($car);

        $video = CarVideo::create([
            'car_id' => $car->id,
            'company_id' => Auth::user()->company_id,
            'video_url' => $request->video_url,
            'platform' => $platform,
            'video_title' => $request->video_title,
            'video_description' => $request->video_description,
            'category' => $request->category ?? 'overview',
            'is_featured' => $request->boolean('is_featured', false),
            'sort_order' => $sortOrder,
        ]);

        // Genereer automatisch thumbnail
        $video->generateThumbnail();

        // Als featured is geselecteerd, maak dit de featured video
        if ($request->boolean('is_featured')) {
            $video->makeFeatured();
        }

        return response()->json([
            'success' => true,
            'message' => 'Video succesvol toegevoegd',
            'video' => [
                'id' => $video->id,
                'video_url' => $video->video_url,
                'embed_url' => $video->embed_url,
                'platform' => $video->platform,
                'platform_video_id' => $video->platform_video_id,
                'video_title' => $video->video_title,
                'video_description' => $video->video_description,
                'category' => $video->category,
                'duration' => $video->duration,
                'thumbnail_url' => $video->thumbnail_url,
                'is_featured' => $video->is_featured,
                'sort_order' => $video->sort_order,
                'created_at' => $video->created_at->format('d-m-Y H:i'),
            ]
        ]);
    }

    /**
     * Update video
     */
    public function update(Request $request, Car $car, CarVideo $video): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        if ($video->car_id !== $car->id) {
            return response()->json([
                'success' => false,
                'message' => 'Video behoort niet tot deze auto'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'video_url' => 'nullable|url',
            'video_title' => 'nullable|string|max:255',
            'video_description' => 'nullable|string|max:1000',
            'category' => 'nullable|in:exterior,interior,engine,test_drive,overview,other',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatiefout',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = $request->only([
            'video_title',
            'video_description',
            'category',
            'sort_order'
        ]);

        // Als URL wordt gewijzigd, update ook platform
        if ($request->has('video_url')) {
            $updateData['video_url'] = $request->video_url;
            $updateData['platform'] = $this->detectPlatform($request->video_url);
        }

        $video->update($updateData);

        // Als featured status wordt gewijzigd
        if ($request->has('is_featured') && $request->boolean('is_featured')) {
            $video->makeFeatured();
        } elseif ($request->has('is_featured') && !$request->boolean('is_featured')) {
            $video->update(['is_featured' => false]);
        }

        // Regenereer thumbnail als URL is gewijzigd
        if ($request->has('video_url')) {
            $video->generateThumbnail();
        }

        return response()->json([
            'success' => true,
            'message' => 'Video succesvol bijgewerkt',
            'video' => [
                'id' => $video->id,
                'video_url' => $video->video_url,
                'embed_url' => $video->embed_url,
                'platform' => $video->platform,
                'platform_video_id' => $video->platform_video_id,
                'video_title' => $video->video_title,
                'video_description' => $video->video_description,
                'category' => $video->category,
                'duration' => $video->duration,
                'thumbnail_url' => $video->thumbnail_url,
                'is_featured' => $video->is_featured,
                'sort_order' => $video->sort_order,
                'updated_at' => $video->updated_at->format('d-m-Y H:i'),
            ]
        ]);
    }

    /**
     * Verwijder video
     */
    public function destroy(Car $car, CarVideo $video): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        if ($video->car_id !== $car->id) {
            return response()->json([
                'success' => false,
                'message' => 'Video behoort niet tot deze auto'
            ], 403);
        }

        $video->delete();

        return response()->json([
            'success' => true,
            'message' => 'Video succesvol verwijderd'
        ]);
    }

    /**
     * Maak video featured
     */
    public function makeFeatured(Car $car, CarVideo $video): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        if ($video->car_id !== $car->id) {
            return response()->json([
                'success' => false,
                'message' => 'Video behoort niet tot deze auto'
            ], 403);
        }

        $video->makeFeatured();

        return response()->json([
            'success' => true,
            'message' => 'Video is nu de featured video'
        ]);
    }

    /**
     * Update sort order van videos
     */
    public function updateSortOrder(Request $request, Car $car): JsonResponse
    {
        // Check if user has access to this car's company
        if (Auth::user()->company_id !== $car->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Geen toegang tot deze auto'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'videos' => 'required|array',
            'videos.*.id' => 'required|integer|exists:car_videos,id',
            'videos.*.sort_order' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validatiefout',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->videos as $videoData) {
            CarVideo::where('id', $videoData['id'])
                ->where('car_id', $car->id)
                ->update(['sort_order' => $videoData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Volgorde succesvol bijgewerkt'
        ]);
    }

    /**
     * Detecteer platform op basis van URL
     */
    private function detectPlatform(string $url): string
    {
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }

        if (str_contains($url, 'vimeo.com')) {
            return 'vimeo';
        }

        return 'direct_url';
    }

    /**
     * Haal volgende sort order op
     */
    private function getNextSortOrder(Car $car): int
    {
        $maxOrder = CarVideo::where('car_id', $car->id)->max('sort_order');
        return ($maxOrder ?? 0) + 1;
    }
}
