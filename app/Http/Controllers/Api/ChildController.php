<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\ChildMeasurement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChildController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $children = Child::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $children]);
    }

    public function show(Request $request, Child $child): JsonResponse
    {
        $child = Child::where('id', $child->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['success' => false, 'message' => 'Profil anak tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $child]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_date' => 'required|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $child = Child::create([
            'user_id' => $request->user()->id,
            ...$validator->validated(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Profil anak berhasil ditambahkan',
            'data' => $child,
        ], 201);
    }

    public function update(Request $request, Child $child): JsonResponse
    {
        $child = Child::where('id', $child->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['success' => false, 'message' => 'Profil anak tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|date|before_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $child->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profil anak berhasil diperbarui',
            'data' => $child,
        ]);
    }

    public function destroy(Request $request, Child $child): JsonResponse
    {
        $child = Child::where('id', $child->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (! $child) {
            return response()->json(['success' => false, 'message' => 'Profil anak tidak ditemukan'], 404);
        }

        $child->delete();

        return response()->json(['success' => true, 'message' => 'Profil anak berhasil dihapus']);
    }

    public function measurements(Request $request, Child $child): JsonResponse
    {
        $child = Child::where('id', $child->id)->where('user_id', $request->user()->id)->first();
        if (! $child) {
            return response()->json(['success' => false, 'message' => 'Profil anak tidak ditemukan'], 404);
        }
        $data = ChildMeasurement::where('child_id', $child->id)->orderBy('measured_at')->get();
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function storeMeasurement(Request $request, Child $child): JsonResponse
    {
        $child = Child::where('id', $child->id)->where('user_id', $request->user()->id)->first();
        if (! $child) {
            return response()->json(['success' => false, 'message' => 'Profil anak tidak ditemukan'], 404);
        }
        $validator = Validator::make($request->all(), [
            'weight' => 'required|numeric|gt:0|lte:50',
            'height' => 'required|numeric|gt:0|lte:200',
            'measured_at' => 'required|date|before_or_equal:today',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        $m = ChildMeasurement::create([
            'child_id' => $child->id,
            ...$validator->validated(),
        ]);
        return response()->json(['success' => true, 'message' => 'Data pengukuran ditambahkan', 'data' => $m], 201);
    }
}