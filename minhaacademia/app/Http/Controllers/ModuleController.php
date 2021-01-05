<?php

namespace App\Http\Controllers;

use App\Module;
use App\Course;
use App\Lesson;
use App\ChangeOrder;
use App\Rules\ModelExists;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleController extends Controller
{
    /**
     * Class LessonController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Module::class, 'module');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function create(Course $course)
    {     
        return view('admin.createModule', [
            'course' => $course
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateModuleCreate($request);

        $module = new Module();
        $module->title = $request->title;
        $module->course = $request->course;
        $module->order = Course::find($request->course)->modulesCount();
        $module->save();

        $course = $module->course();
        $course->updated_at = now();
        $course->save();

        $lesson = new Lesson();
        $lesson->title = 'Nova Aula';
        $lesson->description = 'Descrição da nova aula.';
        $lesson->order = 0;
        $lesson->module = $module->id;
        $lesson->save();

        return redirect()->route('courseEdit', [
            'course' => $request->course,
            '#module-' . $module->id,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Module module
     * @return \Illuminate\Http\Response
     */
    public function edit(Module $module)
    {
        return view('admin.editModule',[
            'module' => $module
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Module $module)
    {
        $this->validateModuleUpdate($request, $module->course());

        $module->title = $request->title;
        $newOrder = $request->order;
        $co = new ChangeOrder($module->course()->modules, $module, $newOrder);
        $co->save();

        $module->save();
        
        return redirect()->route('courseEdit',[
            'course' => $module->course,
            '#module-' . $module->id,
        ]);
    }

    /**
     * Validate forms for create module.
     *
     * @param Request $request
     * @return void
     */
    public function validateModuleCreate(Request $request) {
        $message = 'Não foi possível criar o módulo. Tente novamente mais tarde.';

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'course' => ['required', new ModelExists('App\\Course', $message)],
        ];

        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O :attribute deve ter no máxiomo 255 caracteres',
            'course.required' => 'Não foi possível criar o módulo. Tente novamente mais tarde.',
            'course.min' =>  'Não foi possível criar o módulo. Tente novamente mais tarde.',
        ];

        $attributes = [
            'title' => 'título',
            'course' => 'curso',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

     /**
     * Validate forms for create module.
     *
     * @param Request $request
     * @param Course $course
     * @return void
     */
    public function validateModuleUpdate(Request $request, Course $course) {
        $maxOrder = $course->modulesCount();
        $message = 'a :attribute deve ser um inteiro entre 1 e ' . $maxOrder;

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'order' => ['required', 'integer', 'min:0', 'max:' . ($maxOrder - 1)],
        ];
        
        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O :attribute deve ter no máxiomo 255 caracteres',
            'order.required' => 'É obrigatório informar uma :attribute',
            'order.integer' => $message,
            'order.min' => $message,
            'order.max' => $message,
        ];

        $attributes = [
            'title' => 'título',
            'order' => 'ordem',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Module  $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {   
        $errors = new MessageBag();

        if ($module->course()->modulesCount() < 2) {
            $errors->add('module', 'Não foi possível remover o módulo. Todo curso deve ter pelo menos um módulo.');
            return redirect()->route('courseEdit',[
                'course' => $module->course,
            ])->withErrors($errors);
        }else{
            if ($module->order + 1 < $module->course()->modulesCount()) {
                $nextModuleOrder = $module->order + 1;
            }else{
                $nextModuleOrder = $module->order - 1;
            }

            $module->delete();
            $course = $module->course();
            $course->updated_at = now();
            $course->save();

            foreach ($module->course()->modules as $ord => $mod) {
                if ($mod->order == $nextModuleOrder) {
                    $nextModuleId = $mod->id;
                }
                $mod->order = $ord;
                $mod->save();
            }

            return redirect()->route('courseEdit',[
                'course' => $module->course,
                '#module-' . $nextModuleId,
            ]);
        }
    }
}
