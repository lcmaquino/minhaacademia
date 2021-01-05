<?php

namespace App\Http\Controllers;

use App\Image;
use App\User;
use App\Course;
use App\Lesson;
use App\Activity;
use App\Question;
use App\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

    /**
     * Class ImageController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Image::class, 'image');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateImage($request);

        if ($request->hasFile('image')) {
            $originalName = $request->image->getClientOriginalName();
            $basePath = 'public/images/' . strtolower($request->model) . '/' . $request->modelId;
            $path = $basePath . '/' . $originalName;
            $wasMissing = Storage::disk('local')->missing($path);
            $request->image->storeAs($basePath, $originalName);
            if ($wasMissing) {
                $path = str_replace('public', 'storage', $path);
                $image = Image::create([
                    'path' => $path,
                    'model' => $request->model,
                    'model_id' => $request->modelId,
                ]);
            }
        }
        return redirect()->back()->withInput();
    }

    /**
     * Validate form for store image.
     *
     * @param Request $request
     * @return void
     */
    public function validateImage(Request $request) {
        $defaultMsg = "Não foi possível executar essa requisição. Tente novamente mais tarde.";

        $rules = [
            'model' => ['required', 'in:Course,Lesson,Activity,Question,Item'],
            'modelId' => ['required', 'integer'],
            'image' => ['nullable', 'mimes:png,jpg,jpeg', 'max:2000'],
        ];

        $error_messages = [
            'model.required' => [$defaultMsg],
            'model.in' => [$defaultMsg],
            'modelId.required' => [$defaultMsg],
            'modelId.integer' => [$defaultMsg],
            'image.mimes' => 'A :attribute deve ser png, jpg ou jpeg',
            'image.max' => 'A :attribute deve ter no máximo 2MB',
        ];

        $attributes = [
            'image' => 'imagem',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        $localPath = str_replace('storage', 'public', $image->path);
        Storage::delete($localPath);
        $image->delete();
        return redirect()->back()->withInput();
    }
}
