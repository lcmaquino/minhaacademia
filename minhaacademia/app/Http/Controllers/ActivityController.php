<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Module;
use App\Models\Activity;
use App\Models\Question;
use App\Models\Item;
use App\Filter;
use App\ChangeOrder;
use App\Rules\ModelExists;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Class ActivityController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Activity::class, 'activity');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Module $module)
    {
        return view('admin.createActivity', ['module' => $module]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateActivityCreate($request);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Create
                $activity = new Activity();
                $module = Module::find($request->module);
        
                $activity->title = $request->title;
                $activity->description = $request->description;
                $activity->module = $request->module;
                $activity->order = $module->activitiesCount();
                $activity->save();
        
                $question = new Question();
                $question->content = 'Questão 1';
                $question->answer = 0;
                $question->order = 0;
                $question->activity = $activity->id;
                $question->save();
        
                $item = new Item();
                $item->content = 'Alternativa 1';
                $item->order = 0;
                $item->question = $question->id;
                $item->save();
        
                return redirect()->route('courseEdit', [
                    'course' => $module->course
                ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function show(Activity $activity)
    {
        return view('site.activity', [
            'course' =>   $activity->module()->course(),
            'activity' => $activity,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        return view('admin.editActivity', ['activity' => $activity]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
        $this->validateActivityUpdate($request, $activity);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->description);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Save
                $activity->title = $request->title;
                $activity->description = $request->description;
                $newOrder = $request->order;
                $co = new ChangeOrder($activity->module()->activities, $activity, $newOrder);
                $co->save();
        
                $activity->save();
                
                return redirect()->route('courseEdit', [
                    'course' => $activity->module()->course,
                    '#activity-' . $activity->id,
                ]);
        }
    }

    /**
     * Validate forms for create activity.
     *
     * @param Request $request
     * @return void
     */
    public function validateActivityCreate(Request $request) {
        $message = 'Não foi possível criar a aula. Tente novamente mais tarde.';

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'module' => [new ModelExists('App\\Models\\Module', $message)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O título deve ter no máximo 255 caracteres',
            'description.string' => 'A :attribute deve ser formada por caracteres alfanuméricos',
            'description.max' => 'A :attribute deve ter no máximo 5.000 caracteres',
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'title' => 'título',
            'description' => 'descrição',
            'module' => 'módulo',
            'action' => 'ação',
        ];
        
        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate forms for update activity.
     *
     * @param Request $request
     * @param Activity $activity
     * @return void
     */
    public function validateActivityUpdate(Request $request, Activity $activity) {
        $maxOrder = $activity->module()->activitiesCount();
        $message = 'a :attribute deve ser um inteiro entre 1 e ' . $maxOrder;

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'order' => ['required', 'integer', 'min:0', 'max:' . ($maxOrder - 1)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'title.required' => 'É obrigatório informar um :attribute',
            'title.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'title.max' => 'O título deve ter no máximo 255 caracteres',
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
            'description' => 'descrição',
            'order' => 'ordem',
            'action' => 'ação',
        ];
        
        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {   
        $module = $activity->module();

        if ($module->activitiesCount() < 2) {
            $activity->delete();
            return redirect()->route('courseEdit',[
                'course' => $module->course,
                '#module-' . $module->id  . '-activities',
            ]);
        }else{
            if ($activity->order + 1 < $module->activitiesCount()) {
                $nextActivityOrder = $activity->order + 1;
            }else{
                $nextActivityOrder = $activity->order - 1;
            }

            $activity->delete();
            $module = $activity->module();

            foreach ($module->activities as $ord => $act) {
                if ($act->order == $nextActivityOrder) {
                    $nextActivityId = $act->id;
                }
                $act->order = $ord;
                $act->save();
            }

            return redirect()->route('courseEdit',[
                'course' => $module->course,
                '#activity-' . $nextActivityId,
            ]);
        }
    }

    /**
     * Verifiy user answers for activity. 
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Activity $activity
     * @return \Illuminate\Http\Response
     */
    public function answer(Request $request, Activity $activity){
        $errors = new MessageBag();
        $user = Auth::user();
        $result = 0;

        $s = Setting::where(['key' => 'min_score'])->first();
        $minScore = empty($s) ? 0.75 : intval($s->value)/100.0;

        $message = 'Atividade incompleta. Você precisa acertar no mínimo ' . round(100*$minScore) . '% da atividade.';

        foreach ($activity->questions as $question) {
            $answer = $request->get('question-' . $question->id);
            if ($answer != null && $answer == $question->answer)
                $result++;
        }
        $total = $activity->questionsCount();
        if ($total > 0) {
            if ((1.0*$result)/$total >= $minScore) {
                if (!$activity->isCompleted($user))
                    $activity->storeProgress($user);
            }else{
                $errors->add('activity', $message);
                return redirect()->route('activityShow', [
                    'activity' => $activity,
                ])->withInput($request->all())->withErrors($errors);
            }
        }

        return redirect()->route('activityShow', [
            'activity' => $activity,
        ]);
    }
}