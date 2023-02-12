<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Text;
use Illuminate\Http\Request;

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
        if($request->limit && is_numeric($request->limit) && $request->limit > 0){
            $text = Text::query()->select('text', 'id')->inRandomOrder()->simplePaginate((int)$request->limit);
            $textDelete = Text::query()->whereIn('id', $text->pluck('id'));
            if($textDelete->exists()){
                $textDelete->delete();
            }
            return response()->json([
                'data' => $text,
                'message' => 'Lấy dữ liệu thành công',
            ]);
        }
        $text = Text::query()->select('text', 'id')->inRandomOrder()->first();
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
        return response()->json([
            'data' => [
                'text_count' => $folder->text->count()
            ],
            'message' => 'Lấy giữ liệu thành công!'
        ]);

    }
}
