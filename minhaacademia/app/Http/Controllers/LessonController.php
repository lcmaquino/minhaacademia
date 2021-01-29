<?php

namespace App\Http\Controllers;

use App\Lesson;
use App\Course;
use App\Module;
use App\Filter;
use App\ChangeOrder;
use App\Rules\ModelExists;
use MyYouTubeChannel;
use Lcmaquino\YouTubeChannel\YouTubeVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\MessageBag;

class LessonController extends Controller
{
    /**
     * Class LessonController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Lesson::class, 'lesson');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Module $module)
    {     
        return view('admin.createLesson', ['module' => $module]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateLessonCreate($request);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Create
                $lesson = new Lesson();
                $module = Module::find($request->module);
        
                $lesson->title = $request->title;
                $lesson->video = $request->video;
                $lesson->description = $request->description;
                $lesson->module = $request->module;
                $lesson->order = $module->lessonsCount();
                $lesson->save();
        
                return redirect()->route('courseEdit', [
                    'course' => $module->course,
                    '#lesson-' . $lesson->id,
                ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function show(Lesson $lesson)
    {
        return view('site.lesson',[
            'course' =>   $lesson->module()->course(),
            'lesson' => $lesson,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function edit(Lesson $lesson)
    {
        return view('admin.editLesson', ['lesson' => $lesson]);
    }

    /**
     * Show the form for importing the specified resource.
     * 
     * @param  \App\Course  $course
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function import(Module $module)
    {   
        $user = Auth::user();

        $this->authorize('create', $user);
        
        return view('admin.importLesson', ['module' => $module]);
    }

    /**
     * Store a newly imported resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function importStore(Request $request)
    {
        $user = Auth::user();
        $this->authorize('create', $user);
        $this->validateImport($request);

        $module = Module::find($request->module);
        $video = null;
        $playlist = null;
        $errors = new MessageBag();
        
        if(!empty($user)) {
            $user->refreshTokenIfNeeded();

            if (isset($request->video)) {
                $video = MyYouTubeChannel::video($request->video, $user->access_token);

                if (!empty($video)) {
                    $lesson = $this->videoToLesson($video);
                    $lesson->module = $request->module;
                    $lesson->order = $module->lessons()->count();
                    $lesson->save();
                }else{
                    $errors->add('importVideo', 'Não foi possível importar o vídeo.');
                }
            }

            if (isset($request->playlist)) {
                $start = $request->start ? ($request->start - 1) : 0;
                $end = $request->end ? ($request->end - 1) : null;
                if ($end === null || $start <= $end) {
                    $playlist = MyYouTubeChannel::playlist(
                        $request->playlist,
                        $user->access_token,
                        $start,
                        $end
                    );
    
                    if (!empty($playlist)) {
                        foreach ($playlist->getItems() as $video) {
                            if ($video) {
                                $lesson = $this->videoToLesson($video);
                                $lesson->module = $request->module;
                                $lesson->order = $module->lessons()->count();
                                $lesson->save();
                            }
                        }
                    }
    
                    if (empty($playlist) || empty($playlist->getItems())) {
                        $errors->add('importPlaylist', 'Não foi possível importar a lista de reprodução.');
                    }
                }else{
                    $errors->add('importPlaylist', 'O campo Fim (' . ($end + 1) . ') deve ser maior do que o campo Início (' . ($start + 1) . ')');
                }
            }
        }

        if ($errors->count()) {
            return redirect()->back()->withInput($request->all())->withErrors($errors);
        }else{
            return redirect()->route('courseEdit', [
                'course' => $module->course,
                '#module-' . $module->id . '-lessons',
            ]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lesson $lesson)
    {
        $this->validateLessonUpdate($request, $lesson);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Save
                $lesson->title = $request->title;
                $lesson->video = $request->video;
                $lesson->description = $request->description;           
                $newOrder = $request->order;
                $co = new ChangeOrder($lesson->module()->lessons, $lesson, $newOrder);
                $co->save();
                
                $lesson->save();
                
                return redirect()->route('courseEdit',[
                    'course' => $lesson->module()->course,
                    '#lesson-' . $lesson->id,
                ]);
        }
    }

    /**
     * Convert a YouTubeVideo object in a Lesson object.
     *
     * @param \Lcmaquino\YouTubeChannel\YouTubeVideo $video
     * @return Lesson|null;
     */
    protected function videoToLesson(YouTubeVideo $video){
        $lesson = new Lesson();
        $lesson->title = $video->getTitle();
        $lesson->video = $video->getId();
        $newLinePOSIX = "\n";
        $newLineWin = "\r\n";
        $lesson->description = str_ireplace($newLinePOSIX, $newLineWin, $video->getDescription());
        return $lesson;
    }

    /**
     * Validate forms for create lesson.
     *
     * @param Request $request
     * @return void
     */
    public function validateLessonCreate(Request $request) {
        $message = 'Não foi possível criar a aula. Tente novamente mais tarde.';

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'video' => ['nullable', 'size:11', 'regex:/[a-z0-9_-]{11}/i'],
            'description' => ['nullable', 'string', 'max:5000'],
            'module' => [new ModelExists('App\\Module', $message)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O :attribute deve ter no máximo 255 caracteres',
            'video.size' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'video.regex' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'description.string' => 'A :attribute deve ser formada por caracteres alfanuméricos',
            'description.max' => 'A :attribute deve ter no máximo 5.000 caracteres',
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'title' => 'título',
            'video' => 'vídeo ID',
            'description' => 'descrição',
            'module' => 'módulo',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate forms for update lesson.
     *
     * @param Request $request
     * @param Lesson $lesson
     * @return void
     */
    public function validateLessonUpdate(Request $request, Lesson $lesson) {
        $maxOrder = $lesson->module()->lessonsCount();
        $message = 'a :attribute deve ser um inteiro entre 1 e ' . $maxOrder;

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'video' => ['nullable', 'size:11', 'regex:/[a-z0-9_-]{11}/i'],
            'description' => ['nullable', 'string', 'max:5000'],
            'order' => ['required', 'integer', 'min:0', 'max:' . ($maxOrder - 1)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O :attribute deve ter no máximo 255 caracteres',
            'video.size' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'video.regex' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'description.string' => 'A :attribute deve ser formada por caracteres alfanuméricos',
            'description.max' => 'A :attribute deve ter no máximo 5.000 caracteres',
            'order.required' => 'É obrigatório informar uma :attribute',
            'order.integer' => $message,
            'order.min' => $message,
            'order.max' => $message,
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'title' => 'título',
            'video' => 'vídeo ID',
            'description' => 'descrição',
            'module' => 'módulo',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate form for import lessons.
     *
     * @param Request $request
     * @return void
     */
    public function validateImport(Request $request) {
        $rules = [
            'video' => ['nullable', 'size:11', 'regex:/[a-z0-9_-]{11}/i'],
            'playlist' => ['nullable', 'regex:/[a-z0-9_-]{1,40}/i'],
            'start' => ['nullable', 'integer', 'min:1'],
            'end' => ['nullable', 'integer'],
        ];

        $error_messages = [
            'video.size' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'video.regex' => 'O :attribute deve ter 11 caracteres, com apenas letras, dígitos, - ou _.',
            'playlist.regex' => 'O :attribute deve ter até 40 caracteres, com apenas letras, dígitos, - ou _.',
            'start.integer' => 'O :attribute deve ser um inteiro maior ou igual a 1',
            'start.min' => 'O :attribute deve ser um inteiro maior ou igual a 1',
            'end.integer' => 'O :attribute deve ser um inteiro maior do que 1',
        ];

        $attributes = [
            'video' => 'vídeo ID',
            'playlist' => 'lista de reprodução ID',
            'start' => 'início',
            'end' => 'fim',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Lesson  $lesson
     * @return \Illuminate\Http\Response
     */
    public function destroy(Lesson $lesson)
    {   
        $errors = new MessageBag();
        $module = $lesson->module();

        if ($module->lessonsCount() < 2) {
            $errors->add('lesson', 'Não foi possível remover a aula. Todo módulo deve ter pelo menos uma aula.');
            return redirect()->route('courseEdit',[
                'course' => $module->course,
            ])->withErrors($errors);
        }else{
            if ($lesson->order + 1 < $module->lessonsCount()) {
                $nextLessonOrder = $lesson->order + 1;
            }else{
                $nextLessonOrder = $lesson->order - 1;
            }

            $lesson->delete();
            $module = $lesson->module();

            foreach ($module->lessons as $ord => $lsn) {
                if ($lsn->order == $nextLessonOrder) {
                    $nextLessonId = $lsn->id;
                }
                $lsn->order = $ord;
                $lsn->save();
            }

            return redirect()->route('courseEdit',[
                'course' => $module->course,
                '#lesson-' . $nextLessonId,
            ]);
        }
    }
}