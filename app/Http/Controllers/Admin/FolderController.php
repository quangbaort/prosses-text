<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FolderController extends Controller
{

    public function getFolder(Request $request): \Illuminate\Http\JsonResponse
    {
        $folders = Folder::query()->orderBy('created_at', 'desc')->paginate(15);
        return response()->json(['data' => $folders, 'message' => 'lấy giữ liệu thành công']);
    }

    public function addFolder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Tên danh sách không được để trống!',
            'name.string' => 'Tên danh sách phải là ký tự',
            'name.max' => 'Tên danh sách không được vượt quá 255 ký tự',
        ]);
        $folderId = Folder::query()->create([
           'name' => $request->name,
           'api' => Str::slug($request->name),
        ])->id;
        return response()->json([
            'data' => [
                'folder_id' => $folderId
            ],
            'message' => 'Thêm mới thành công'
        ]);
    }
}
