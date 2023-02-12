<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Text;
use Illuminate\Http\Request;

class UploadController extends Controller
{

    public function upload(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            // 25.5 mb
            'file' => 'required|mimes:txt|max:26624',
        ]);
        // read file
        $file = $request->file('file');
        try {
            $data = [];
            $file->storeAs('public/files', $file->getClientOriginalName());
            foreach (file($file) as $line) {
                $data[] = [
                    'text' => $line,
                ];
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


        return back()
            ->with('success', 'Bạn đã upload file thành công')
            ->with('file', $file->getClientOriginalName())
            ->with('count', count(file($file)));

    }

    public function countRow(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            // 25.5 mb
            'file' => 'required|mimes:txt|max:26624',
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
}
