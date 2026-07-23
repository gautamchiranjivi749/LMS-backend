<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Skill;
use App\Http\Requests\StoreSkillRequest;
use App\Http\Requests\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;

class SkillController extends Controller
{
        use ApiResponse;
    /**
     * Display a listing of the resource.
     */
   public function index(Request $request)
{
    $query = Skill::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $skills = $query->latest()->paginate(10);

    return $this->success(
        'Skills retrieved successfully.',
        [
            'skills' => SkillResource::collection($skills),
            'pagination' => [
                'current_page' => $skills->currentPage(),
                'last_page' => $skills->lastPage(),
                'per_page' => $skills->perPage(),
                'total' => $skills->total(),
            ]
        ]
    );
}

    /**
     * Store a newly created resource in storage.
     */
   public function store(StoreSkillRequest $request)
{
    $skill = Skill::create([
        'name' => $request->name,
        'slug' => Str::slug($request->name),
        'description' => $request->description,
        'status' => $request->boolean('status', true),
    ]);

    return $this->success(
        'Skill created successfully.',
        new SkillResource($skill),
        201
    );
}
    /**
     * Display the specified resource.
     */
    public function show(Skill $skill)
    {
        return $this->success(
            'Skill retrieved successfully.',
            new SkillResource($skill)
        );
    }

    /**
     * Update the specified resource in storage.
        */
    public function update(UpdateSkillRequest $request, Skill $skill)
    {
        $skill->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->boolean('status'),
        ]);

        return $this->success(
            'Skill updated successfully.',
            new SkillResource($skill->fresh())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Skill $skill)
    {
        $skill->delete();

        return $this->success(
            'Skill deleted successfully.'
        );
    }
        public function restore($id)
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);

        $skill->restore();

        return $this->success(
            'Skill restored successfully.',
            new SkillResource($skill)
        );
    }
        public function forceDelete($id)
    {
        $skill = Skill::onlyTrashed()->findOrFail($id);

        $skill->forceDelete();

        return $this->success(
            'Skill permanently deleted.'
        );
    }
}
