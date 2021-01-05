<?php

namespace App\Http\Controllers;

use App\Course;
use App\Module;
use App\Lesson;
use App\Activity;
use App\Question;
use App\Item;
use App\Certify;
use App\Filter;
use App\Rules\CourseJson;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * Class CourseController Constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Course::class, 'course');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();

        if (!empty($user) && !$user->isStudent()) {
            $courses = Course::all()->sortByDesc('created_at');
        }else{
            $courses = Course::where(['visibility' => '1'])->get()->sortByDesc('created_at');
        }

        return view('site.courses',[
            'courses' => $courses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {   
        return view('admin.createCourse');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateCourse($request);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Create
                $course = new Course();
                $course->title = $request->title;
                $course->video = $request->video;
                $course->description = $request->description;
                $course->duration = $request->duration;
                $course->visibility = $request->visibility;
                $course->teacher = Auth::id();
                $course->save();
        
                $module = new Module();
                $module->title = "Módulo 1";
                $module->course = $course->id;
                $module->order = 0;
                $module->save();
        
                $lesson = new Lesson();
                $lesson->title = 'Nova Aula';
                $lesson->description = 'Descrição da nova aula.';
                $lesson->order = 0;
                $lesson->module = $module->id;
                $lesson->save();
        
                return redirect()->route('courseShow', ['course' => $course]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function show(Course $course)
    {
        return view('site.course', ['course' => $course]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        return view('admin.editCourse',[
            'course' => $course
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $this->validateCourse($request);
        
        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Save
                $course->title = $request->title;
                $course->video = $request->video;
                $course->description = $request->description;
                $course->duration = $request->duration;
                $course->visibility = $request->visibility;

                if ($request->hasFile('icon')) {
                    $filename = $request->icon->getClientOriginalName();
                    $baseRealPath = 'public/images/course/' . $course->id . '/icon';
                    $realPath = $baseRealPath . '/' . $filename;
                    $path = str_replace('public', 'storage', $realPath);
                    $request->icon->storeAs($baseRealPath, $filename);
                    if (empty($course->icon)) {
                        $course->icon = [
                            'filename' => $filename,
                            'realPath' => $realPath,
                            'path' => $path,
                        ];
                    }else{
                        if($course->icon['realPath'] != $realPath) {
                            $oldRealPath = $course->icon['realPath'];
                            $course->icon = [
                                'filename' => $filename,
                                'realPath' => $realPath,
                                'path' => $path,
                            ];
                            Storage::delete($oldRealPath);
                        }
                    }
                }

                $course->save();
        
                return redirect()->route('courseEdit', ['course' => $course]);
        }
    }

    /**
     * Validate forms for create/update course.
     *
     * @param Request $request
     * @return void
     */
    public function validateCourse(Request $request) {
        $defaultMsg = "Não foi possível executar essa requisição. Tente novamente mais tarde.";
        $rules = [
            'icon' => ['nullable', 'max:500', 'mimes:png,jpg,jpeg'],
            'title' => ['required', 'string', 'max:255'],
            'video' => ['nullable', 'size:11', 'regex:/[a-z0-9_-]{11}/i'],
            'duration' => ['required', 'integer', 'gt:0', 'max:255'],
            'visibility' => ['required', 'in:0,1'],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'icon.mimes' => 'O :attribute deve ser png, jpg ou jpeg',
            'icon.max' => 'O :attribute deve ter no máximo 500KB',
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve conter apenas caracteres alfanuméricos',
            'title.max' => 'O :attribute deve ter no máxiomo 255 caracteres',
            'video.size' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'video.regex' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'duration.required' => 'É obrigatório informar uma :attribute',
            'duration.integer' => 'A :attribute deve ser um número inteiro',
            'duration.gt' => 'A :attribute deve ser maior do que 0 horas',
            'duration.max' => 'A :attribute deve ser no máximo 255 horas',
            'visibility.required' => 'É obrigatório informar a :attribute',
            'visibility.in' => ':attribute deve ser Público ou Privado',
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'icon' => 'ícone',
            'title' => 'título',
            'video' => 'vídeo ID',
            'duration' => 'carga horária',
            'visibility' => 'visibilidade',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

     /**
     * Import course in json file.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request) {
        $this->validateCourseImport($request);
        $this->authorize('create', Auth::user());

        if ($request->file->isValid()) {
            $data = json_decode($request->file->get());
            $course = Course::create([
                'title' => $data->title,
                'description' => $data->description,
                'video' => $data->video,
                'duration' => $data->duration,
                'visibility' => $data->visibility,
                'teacher' => Auth::id(),
            ]);
            
            foreach($data->modules as $module) {
                $mod = Module::create([
                    'title' => $module->title,
                    'order' => $module->order,
                    'course' => $course->id,
                ]);

                foreach ($module->lessons as $lesson) {
                    $lsn = Lesson::create([
                        'title' => $lesson->title,
                        'video' => $lesson->video,
                        'description' => $lesson->description,
                        'order' => $lesson->order,
                        'module' => $mod->id,
                    ]);
                }
                
                if(!empty($module->activities)) {
                    foreach ($module->activities as $activity) {
                        $act = Activity::create([
                            'title' => $activity->title,
                            'description' => $activity->description,
                            'order' => $activity->order,
                            'module' => $mod->id,
                        ]);

                        foreach ($activity->questions as $question) {
                            $qtn = Question::create([
                                'content' => $question->content,
                                'answer' => $question->answer,
                                'order' => $question->order,
                                'activity' => $act->id,
                            ]);

                            foreach ($question->items as $item) {
                                $it = Item::create([
                                    'content' => $item->content,
                                    'order' => $item->order,
                                    'question' => $qtn->id,
                                ]);
                            }
                        }
                    }
                }
            }

            return redirect()->route('courseEdit', ['course' => $course]);
        }else{
            $errors = new MessageBag();
            $errors->add('import_course', 'Não foi possível importar o curso com este arquivo.');
            return redirect()->back()->withInput()->withErrors($errors);
        }
    }

    /**
     * Validate form for import course.
     *
     * @param Request $request
     * @return void
     */
    public function validateCourseImport(Request $request) {
        $rules = [
            'file' => ['required', 'max:5000', new CourseJson('Não foi possível importar o curso com este :attribute.')],
        ];

        $error_messages = [
            'file.required' => 'É obrigatório informar um :attribute',
            'file.max' => 'O :attribute deve ter no máximo 5MB',
        ];

        $attributes = [
            'file' => 'arquivo',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Export course as json file.
     *
     * @param Request $request
     * @param Course $course
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, Course $course) {
        $filename = Str::slug($course->title) . '.json';
        $path = 'json/';
        $data['title'] = $course->title;
        $data['video'] = $course->video;
        $data['description'] = $course->description;
        $data['duration'] = $course->duration;

        foreach($course->modules as $module) {
            $data['modules'][] = [
                'title' => $module->title,
                'order' => $module->order,
            ];

            foreach ($module->lessons as $lesson) {
                $data['modules'][$module->order]['lessons'][] = [
                    'title' => $lesson->title,
                    'video' => $lesson->video,
                    'description' => $lesson->description,
                    'order' => $lesson->order,
                ];
            }

            foreach ($module->activities as $activity) {
                $data['modules'][$module->order]['activities'][] = [
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'order' => $activity->order,
                ];

                foreach ($activity->questions as $question) {
                    $data['modules'][$module->order]['activities'][$activity->order]['questions'][] = [
                        'content' => $question->content,
                        'answer' => $question->answer,
                        'order' => $question->order,
                    ];

                    foreach ($question->items as $item) {
                        $data['modules'][$module->order]['activities'][$activity->order]['questions'][$question->order]['items'][] = [
                            'content' => $item->content,
                            'order' => $item->order,
                        ];
                    }
                }
            }
        }

        $dataJson = json_encode($data, JSON_PRETTY_PRINT);
        Storage::disk('local')->put($path . $filename, $dataJson);
        $fullpath = storage_path('app/' . $path . $filename);

        return response()->download($fullpath, $filename, ['Content-type' => 'application/json'])->deleteFileAfterSend();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('courses');
    }
}