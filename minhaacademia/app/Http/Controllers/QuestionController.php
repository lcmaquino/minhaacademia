<?php

namespace App\Http\Controllers;

use App\Question;
use App\Activity;
use App\Item;
use App\Image;
use App\Filter;
use App\ChangeOrder;
use App\Rules\ModelExists;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Class QuestionController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Question::class, 'question');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Activity $activity)
    {
        return view('admin.createQuestion', ['activity' => $activity]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateQuestionCreate($request);
     
        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->content);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Create
                $question = new Question();
                $activity = Activity::find($request->activity);
        
                $question->content = $request->content;
                $question->answer = $request->answer;
                $question->order = $activity->questions()->count();
                $question->activity = $request->activity;
                $question->save();
        
                for($i = 0; $i < $request->answer + 1; $i++) {
                    $item = new Item();
                    $item->content = 'Alternativa ' . ($i + 1);
                    $item->order = $i;
                    $item->question = $question->id;
                    $item->save();
                }
        
                return redirect()->route('activityEdit', [
                    'activity' => $request->activity
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        return view('admin.editQuestion', ['question' => $question]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        $this->validateQuestionUpdate($request, $question);
        
        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->content);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Save
                $question->content = $request->content;
                $question->answer = $request->answer;
                $newOrder = $request->order;
                $co = new ChangeOrder($question->activity()->questions, $question, $newOrder);
                $co->save();

                $question->save();
                
                return redirect()->route('activityEdit', [
                    'activity' => $question->activity,
                    '#question-' . $question->id,
                ]);
        }
    }

    /**
     * Validate forms for create question.
     *
     * @param Request $request
     * @return void
     */
    public function validateQuestionCreate(Request $request) {
        $message = 'Não foi possível criar a questão. Tente novamente mais tarde.';

        $rules = [
            'content' => ['required', 'string', 'max:5000'],
            'answer' => ['required', 'integer', 'min:0', 'max:26'],
            'activity' => [new ModelExists('App\\Activity', $message)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'content.required' => 'É obrigatório informar um :attribute',
            'content.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'content.max' => 'O :attribute deve ter no máximo 5.000 caracteres',
            'answer.required' => 'É obrigatório informar uma :attribute',
            'answer.min' => 'A :attribute deve ser uma letra maiúscula do alfabeto romano',
            'answer.max' => 'A :attribute deve ser uma letra maiúscula do alfabeto romano',
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'content' => 'enunciado',
            'answer' => 'resposta',
            'activity' => 'atividade',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate forms for update question.
     *
     * @param Request $request
     * @param Question $question
     * @return void
     */
    public function validateQuestionUpdate(Request $request, Question $question) {
        $maxOrder = $question->activity()->questionsCount();
        $message = 'a :attribute deve ser um inteiro entre 1 e ' . $maxOrder;

        $rules = [
            'content' => ['required', 'string', 'max:5000'],
            'answer' => ['required', 'integer', 'min:0', 'max:26'],
            'order' => ['required', 'integer', 'min:0', 'max:' . ($maxOrder - 1)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'content.required' => 'É obrigatório informar um :attribute',
            'content.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'content.max' => 'O :attribute deve ter no máximo 5.000 caracteres',
            'answer.required' => 'É obrigatório informar uma :attribute',
            'answer.min' => 'A :attribute deve ser uma letra maiúscula do alfabeto romano',
            'answer.max' => 'A :attribute deve ser uma letra maiúscula do alfabeto romano',
            'order.required' => 'É obrigatório informar uma :attribute',
            'order.integer' => $message,
            'order.min' => $message,
            'order.max' => $message,
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'content' => 'enunciado',
            'answer' => 'resposta',
            'order' => 'ordem',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {   
        $errors = new MessageBag();
        $activity = $question->activity();

        if ($activity->questionsCount() < 2) {
            $errors->add('activity', 'Não foi possível remover a questão. Toda atividade deve ter pelo menos uma questão.');
            return redirect()->route('activityEdit',[
                'activity' => $activity->id,
            ])->withErrors($errors);
        }else{
            if ($question->order + 1 < $activity->questionsCount()) {
                $nextQuestionOrder = $question->order + 1;
            }else{
                $nextQuestionOrder = $question->order - 1;
            }

            $question->delete();
            
            $activity = $question->activity();
            foreach ($activity->questions as $ord => $qtn) {
                if ($qtn->order == $nextQuestionOrder) {
                    $nextQuestionId = $qtn->id;
                }
                $qtn->order = $ord;
                $qtn->save();
            }

            return redirect()->route('activityEdit',[
                'activity' => $activity->id,
                '#question-' . $nextQuestionId,
            ]);
        }
    }
}
