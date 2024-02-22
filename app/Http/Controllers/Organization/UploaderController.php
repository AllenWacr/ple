<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Intervention\Image\Facades\Image;

class UploaderController extends Controller
{
    public function image(Request $request){
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // 儲存原始圖片
            $file->move(public_path('media'), $fileName);

            // 使用 Intervention Image 來調整尺寸
            $image = Image::make(public_path('media/' . $fileName))->fit(300, 300);

            // 轉換為 base64
            $base64Image = (string) $image->encode('data-url');

            // 使用 Spatie Image Optimizer
            $optimizerChain = OptimizerChainFactory::create();
            $optimizerChain->optimize(public_path('media/' . $fileName));

            $url = asset('media/' . $fileName);

            return response()->json(['fileName' => $fileName, 'uploaded' => 1, 'url' => $url, 'base64' => $base64Image]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
