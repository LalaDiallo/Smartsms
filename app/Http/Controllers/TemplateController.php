<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Categorie;
use App\Models\Templates;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TemplateController extends Controller
{

    public function index(Request $request)
    {
        $client = auth()->user()->client_id;

        $query = Templates::where('client_id', $client)
            ->with('category', 'tags');

        // 🔥 Filtrage par canal (sms, email, whatsapp, push)
        if ($request->filled('channel')) {
            $query->where('channel', $request->channel);
        }

        // (optionnel) uniquement les templates actifs
        if ($request->filled('active')) {
            $query->where('is_active', $request->active);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'tem' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        // Validation minimale
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'channel' => 'required|in:sms,email,whatsapp,push',
            'category' => 'required|string|min:1|max:255', // Ensure non-empty string
            'tags' => 'nullable|array',
            'tags.*' => 'string',
            'extrait' => 'required|string|max:255',
            'sector' => 'required|string|max:255',
        ]);

        // Ensure category is not empty or null
        if (empty(trim($validated['category']))) {
            return response()->json([
                'message' => 'The category field cannot be empty.',
                'errors' => ['category' => ['The category field cannot be empty.']],
            ], 422);
        }

        // --- Gestion catégorie ---
        $category = Categorie::firstOrCreate(
            ['name' => $validated['category']],
            ['slug' => Str::slug($validated['category'])]
        );

        // --- Création du template ---
        $template = Templates::create([
            'name' => $validated['name'],
            'extrait' => $validated['extrait'] ?: substr($validated['content'], 0, 50),
            'content' => $validated['content'],
            'channel' => $validated['channel'],
            'category_id' => $category->id,
            'client_id' => auth()->user()->client_id,
            'sector' => $validated['sector']
        ]);

        // --- Gestion des tags ---
        if ($request->has('tags')) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                if (!empty(trim($tagName))) { // Skip empty tags
                    $tag = Tag::firstOrCreate(
                        ['name' => $tagName],
                        ['slug' => Str::slug($tagName)]
                    );
                    $tagIds[] = $tag->id;
                }
            }
            // Attacher les tags au template
            $template->tags()->sync($tagIds);
        }

        return response()->json([
            'message' => 'Template créé avec succès',
            'data' => $template->load('category', 'tags')
        ], 201);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $template = Templates::findOrFail($id);

        // Ensure the template belongs to the authenticated user's client
        if ($template->client_id !== auth()->user()->client_id) {
            return response()->json([
                'message' => 'Unauthorized access to this template.'
            ], 403);
        }

        $template->update([
            'is_favori' => !$template->is_favori,
        ]);

        return response()->json([
            'message' => $template->is_favori ? 'Template added to favorites' : 'Template removed from favorites',
            'data' => $template->load('category', 'tags')
        ], 200);
    }

    public function show($id) {
        return Templates::with('category','tags')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'channel' => ['required', Rule::in(['sms', 'email', 'whatsapp', 'push'])],
            'category' => ['required', 'string'],
            'extrait' => ['required', 'string', 'max:255'],
            'sector' => ['required', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:255'],
            'is_favori' => ['boolean'],
        ]);

        try {
            // Start a transaction to ensure data consistency
            return DB::transaction(function () use ($id, $data) {
                // Find the template or fail
                $template = Templates::findOrFail($id);

                // Resolve or create category
                $category = Categorie::firstOrCreate(
                    ['name' => $data['category']],
                    ['slug' => Str::slug($data['category'])]
                );

                // Update template fields
                $template->update([
                    'name' => $data['name'],
                    'content' => $data['content'],
                    'channel' => $data['channel'],
                    'category_id' => $category->id,
                    'extrait' => $data['extrait'],
                    'sector' => $data['sector'],
                    'is_favori' => $data['is_favori'] ?? false,
                ]);

                // Sync tags (create if they don't exist)
                $tagIds = [];
                if (isset($data['tags']) && is_array($data['tags'])) {
                    foreach ($data['tags'] as $tagName) {
                        $tag = Tag::firstOrCreate(
                            ['name' => $tagName],
                            ['slug' => Str::slug($tagName)]
                        );
                        $tagIds[] = $tag->id;
                    }
                    $template->tags()->sync($tagIds);
                } else {
                    $template->tags()->sync([]); // Clear tags if none provided
                }

                // Load relationships for response
                $template->load('category', 'tags');

                // Transform response to match frontend expectations
                $responseData = [
                    'id' => $template->id,
                    'name' => $template->name,
                    'extrait' => $template->extrait,
                    'content' => $template->content,
                    'channel' => $template->channel,
                    'category' => $template->category->name,
                    'tags' => $template->tags->pluck('name'),
                    'sector' => $template->sector,
                    'created_at' => $template->created_at,
                    'updated_at' => $template->updated_at,
                    'is_favori' => $template->is_favori,
                ];

                return response()->json([
                    'data' => $responseData,
                    'message' => 'Template mis à jour avec succès !',
                ], 200);
            });
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Erreur lors de la mise à jour du template: ' . $e->getMessage());

            return response()->json([
                'message' => 'Une erreur est survenue lors de la mise à jour du template.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id) {
        $template = Templates::findOrFail($id);
        $template->delete();
        return response()->json(['message' => 'Template supprimé']);
    }
}
