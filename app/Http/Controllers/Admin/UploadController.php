<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use App\Models\Text;
use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class UploadController extends Controller
{

    public function upload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            // 2gb
            'file' => 'required|mimes:txt|max:2097152',
            'folder_id' => 'required|exists:folders,_id'
        ],[
            'file.required' => 'file không đượic để trống',
            'file.mimes' => 'file sai định dang txt',
            'file,max' => 'file quá tải, không được vượt quá 2gb',
            'folder_id.required' => 'folder_id không được để trống',
            'folder_id.exit' => 'id folder không tồn tại',
        ]);
        $file = null;
        $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        if(!$receiver->isUploaded()) {
            return response()->json([
                'data' => [],
                'message' => 'Tải lên thất bại'
            ]);
        }
        $fileReceiver = $receiver->receive();
        if($fileReceiver->isFinished()) {
            $file = $fileReceiver->getFile();
            $file->storeAs('public/files', $file->getClientOriginalName());
            $data = [];
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
            'folder_api' => 'required|string|exists:folders,_id|max:255'
        ], [
            'folder_api.required' => 'Folder api không được bỏ trống!',
            'folder_api.string' => 'Folder api phải là ký tự!',
            'folder_api.exists' => 'Folder api không tồn tại!',
            'folder_api.max' => 'Folder api không được vượt quá 255 ký tự!'

        ]);
        $text = Text::query()->where('folder_id', $request->folder_api)->first();
        if(!$text) {
            return response()->json([
                'data' => [],
                'message' => 'Không có dữ liệu'
            ]);
        }
        // delete text
        $text->find($text->_id)->delete();
        return response()->json([
            'data' => $text,
            'message' => 'Lấy dữ liệu thành công',
        ]);
    }

    public function getRowName(Request $request, $idFolder): \Illuminate\Http\JsonResponse
    {

        $folder = Folder::query()->findOrFail($idFolder);
        $textCount = Text::query()->where('folder_id', $idFolder)->count();
        return response()->json([
            'data' => [
                'text_count' => number_format($textCount),
                'link_api' => env('APP_URL').'/api/get-row?folder_api='.$folder->_id,
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

        $texts = Text::query()->where('folder_id', $idFolder);
        $textCount = $texts->count();

        if($textCount <= 0) {
            return response()->json([
                'data' => [],
                'message' => 'Đã xóa hết'
            ]);
        }
        if($texts->exists()){
            $texts->chunkById(1000, function ($texts) {
                foreach ($texts as $text) {
                    $text->delete();
                }
            });
        }
        $folder->delete();
        return response()->json([
            'data' => [],
            'message' => 'Đã xóa '. number_format($textCount) . ' dòng'
        ]);
    }
}
