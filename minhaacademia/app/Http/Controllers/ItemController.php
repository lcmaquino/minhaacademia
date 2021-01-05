<?php

namespace App\Http\Controllers;

use App\Item;
use App\Question;
use App\Filter;
use App\ChangeOrder;
use App\Rules\ModelExists;
use Illuminate\Support\MessageBag;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Class ItemController Constructor.
    */
    public function __construct()
    {
        $this->authorizeResource(Item::class, 'item');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Question $question)
    {
        return view('admin.createItem', ['question' => $question]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateItemCreate($request);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->content, [
                    'addLaTeX',
                    'scapeHtmlSpecialChars',
                    'addLinks',
                    'addImages',
                    'addRichFormat',
                ]);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Create
                $item = new Item();
                $question = Question::find($request->question);
        
                $item->content = $request->content;
                $item->order = $question->items()->count();
                $item->question = $request->question;
                $item->save();
        
                return redirect()->route('activityEdit', [
                    'activity' => $question->activity,
                    '#question-' . $question->id,
                ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        return view('admin.editItem', ['item' => $item]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        $this->validateItemUpdate($request, $item);

        switch ($request->action){
            case "Pré-visualizar":
                $f = new Filter($request->content, [
                    'addLaTeX',
                    'scapeHtmlSpecialChars',
                    'addLinks',
                    'addImages',
                    'addRichFormat',
                ]);
                $inputs = $request->input();
                $inputs['preview'] = $f->render();
                return redirect()->back()->withInput($inputs);
            default: //Save
                $item->content = $request->content;
                $newOrder = $request->order;
                $co = new ChangeOrder($item->question()->items, $item, $newOrder);
                $co->save();

                $item->save();
                
                return redirect()->route('activityEdit', [
                    'activity' => $item->question()->activity,
                    '#question-' . $item->question()->id,
                ]);
        }
    }

    /**
     * Validate forms for create item.
     *
     * @param Request $request
     * @return void
     */
    public function validateItemCreate(Request $request) {
        $message = 'Não foi possível criar o item. Tente novamente mais tarde.';

        $rules = [
            'content' => ['required', 'string', 'max:5000'],
            'question' => [new ModelExists('App\\Question', $message)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'content.required' => 'É obrigatório informar o :attribute',
            'content.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'content.max' => 'O :attribute deve ter no máximo 5.000 caracteres',
            'question.integer' => 'Não foi possível adicionar o item. Tente novamente mais tarde.',
            'question.min' => 'Não foi possível adicionar o item. Tente novamente mais tarde.',
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'content' => 'conteúdo',
            'question' => 'questão',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Validate forms for create item.
     *
     * @param Request $request
     * @param Item $item
     * @return void
     */
    public function validateItemUpdate(Request $request, Item $item) {
        $maxOrder = $item->question()->itemsCount();
        $message = 'o :attribute deve ser um inteiro entre 1 e ' . $maxOrder;

        $rules = [
            'content' => ['required', 'string', 'max:5000'],
            'order' => ['required', 'integer', 'min:0', 'max:' . ($maxOrder - 1)],
            'action' => ['required', 'in:Salvar,Pré-visualizar'],
        ];

        $error_messages = [
            'content.required' => 'É obrigatório informar o :attribute',
            'content.string' => 'O :attribute deve ser formado por caracteres alfanuméricos',
            'content.max' => 'O :attribute deve ter no máximo 5.000 caracteres',
            'order.required' => 'É obrigatório informar uma :attribute',
            'order.integer' => $message,
            'order.min' => $message,
            'order.max' => $message,
            'action.required' => 'É obrigatório informar uma :attribute',
            'action.in' => ':attribute inválida',
        ];

        $attributes = [
            'content' => 'conteúdo',
            'order' => 'ordem',
            'action' => 'ação',
        ];

        $request->validate($rules, $error_messages, $attributes);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $errors = new MessageBag();
        $question = $item->question();
        $answer = $question->answer;
        $answerItem = $question->answerItem();

        if ($item->order == $answer) {
            $errors->add('item', 'Não foi possível remover o item, pois ele é a resposta da questão');
        }

        if ($errors->count() === 0) {
            $item->delete();
            $newOrder = 0;
            foreach ($question->items as $ord => $it) {
                $it->order = $ord;
                $it->save();
                if ($it->id == $answerItem->id)
                    $newOrder = $ord;
            }
            $question->answer = $newOrder;
            $question->save();
        }

        return redirect()->route('activityEdit',[
            'activity' => $question->activity,
            '#question-' . $question->id,
        ])->withErrors($errors);
    }
}
