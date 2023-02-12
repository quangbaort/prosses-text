<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Text;
use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends Controller
{

    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            // 2gb
            'file' => 'required|mimes:txt|max:2097152',
            'folder_id' => 'required|exists:folders,id'
        ],[
            'file.required' => 'file không đượic để trống',
            'file.mimes' => 'file sai định dang txt',
            'file,max' => 'file quá tải, không được vượt quá 2gb',
            'folder_id.required' => 'folder_id không được để trống',
            'folder_id.exit' => 'id folder không tồn tại',
        ]);
//        $receiver = new FileReceiver('file', $request, )
        // read file
        $file = $request->file('file');
        try {
            $data = [];
            $file->storeAs('public/files', $file->getClientOriginalName());
            foreach (file($file) as $line) {
                if($line !== '') {
                    $data[] = [
                        'text' => $line,
                        'folder_id' => $request->folder_id
                    ];
                }
            }
            $collection = collect($data)->chunk(1000);
            foreach ($collection as $chunk) {
                Text::query()->insert($chunk->toArray());
            }
            // delete file from storage
            unlink(storage_path('app/public/files/' . $file->getClientOriginalName()));
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return response()->json([
            'data' => [],
            'message' => 'Tải lên thành công file '. $file->getClientOriginalName(). ' '. count(file($file)) .' dòng'
        ]);

    }

    public function countRow(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            // 2gb
            'file' => 'required|mimes:txt|max:2097152',
        ]);
        $file = $request->file('file');
        return response()->json([
            'count' => count(file($file)),
        ]);
    }

    public function getRow(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'limit' => 'numeric',
            'folder_api' => 'required|string|exists:folders,api|max:255'
        ], [
            'folder_api.required' => 'Folder api không được bỏ trống!',
            'folder_api.string' => 'Folder api phải là ký tự!',
            'folder_api.exists' => 'Folder api không tồn tại!',
            'folder_api.max' => 'Folder api không được vượt quá 255 ký tự!'

        ]);
        $folder = Folder::query()->where('api', $request->folder_api)->first();
        $text = Text::query()->where('folder_id', $folder->id)->select('text', 'id')->inRandomOrder()->first();
        $textDelete = Text::query()->where('id', $text->id);
        if($textDelete->exists()){
            $textDelete->delete();
        }
        return response()->json([
            'data' => $text,
            'message' => 'Lấy dữ liệu thành công',
        ]);
    }

    public function getRowName(Request $request, $idFolder)
    {

        $folder = Folder::query()->findOrFail($idFolder);
        $textCount = Text::query()->where('folder_id', $idFolder)->count();
        return response()->json([
            'data' => [
                'text_count' => number_format($textCount),
                'link_api' => env('APP_URL').'/api/get-row?folder_api='.$folder->api
            ],
            'message' => 'Lấy giữ liệu thành công!'
        ]);

    }
    public function deleteText(Request $request, $idFolder): \Illuminate\Http\JsonResponse
    {
        if(!$idFolder && !is_numeric($idFolder)){
            return response()->json([
                'data' => [],
                'message' => 'id không đúng'
            ]);
        }
        $folder = Folder::query()->findOrFail($idFolder);
        $folder->delete();
        $texts = Text::query()->where('folder_id', $idFolder);
        $textCount = $texts->count();

        if($textCount <= 0) {
            return response()->json([
                'data' => [],
                'message' => 'Đã xóa hết'
            ]);
        }
        if($texts->exists()){
            $texts->delete();
        }
        return response()->json([
            'data' => [],
            'message' => 'Đã xóa '. number_format($textCount) . ' dòng'
        ]);
    }
}
