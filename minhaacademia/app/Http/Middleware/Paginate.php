<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Certify;
use App\Setting;
use Illuminate\Support\Facades\Auth;

class Paginate
{
    protected $model;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $param = [];
        $this->model = null;
        if (in_array($request->query('maxResults'), ['5', '30', '50'])) {
            $maxResults = $request->query('maxResults');
        }else{
            $maxResults = 30;
        }
        $param['maxResults'] = $maxResults;
        $filter = $request->query('filter');

        $controllerClass = class_basename(get_class($request->route()->getController()));
        $method = 'get' . $controllerClass . 'Paginate';

        if (method_exists(new Paginate(), $method)) {
            $request = call_user_func(Paginate::class . '::'. $method, $request, $filter, $maxResults);
        }

        $page = $request->query('page');
        if (filter_var($page, FILTER_VALIDATE_INT) === false || $page < 0) {
            $page = 1;
        }

        if (!empty($filter))
            $param['filter'] = $filter;

        if ($this->model->total() > 0) {
            $query = empty($param) ? '' : '&' . http_build_query($param);
            $pagNavMenuItems = [
                $page == 1 ? '<i class="fa gray fa-step-backward"></i>' : '<a href="' . $this->model->url(1) . $query . '" title="Início"><i class="fa fa-step-backward"></i></a>',
                $page == 1 ? '<i class="fa gray fa-chevron-left"></i>' : '<a href="'. $this->model->previousPageUrl() . $query . '" title="Anterior"><i class="fa fa-chevron-left"></i></a>',
                $page >= $this->model->lastPage() ? '<i class="fa gray fa-chevron-right"></i>' : '<a href="'. $this->model->nextPageUrl() . $query . '" title="Próximo"><i class="fa fa-chevron-right"></i></a>',
                $page >= $this->model->lastPage() ? '<i class="fa gray fa-step-forward"></i>' : '<a href="'. $this->model->url($this->model->lastPage()) . $query . '" title="Último"><i class="fa fa-step-forward"></i></a>',
            ];
        }else{
            $pagNavMenuItems = [
                '<i class="fa gray fa-step-backward"></i>',
                '<i class="fa gray fa-chevron-left"></i>',
                '<i class="fa gray fa-chevron-right"></i>',
                '<i class="fa gray fa-step-forward"></i>',
            ];
        }

        $param['pagNavMenuItems'] = $pagNavMenuItems;

        $request->merge($param);

        return $next($request);
    }

    /**
     * Get paginate for users' page.
     *
     * @param Request $request
     * @param $filter
     * @param integer $maxResults
     * @param string $model
     * @return Request
     */
    protected function getUserControllerPaginate($request, $filter = null, $maxResults = 30, $model = 'users') {
        if(empty($filter)) {
            $users = User::paginate($maxResults);
        }else{
            $users = User::where('name', 'like', '%' . $filter . '%')
                        ->orWhere('email', 'like', '%' . $filter . '%')
                        ->paginate($maxResults);
        }
        $this->model = $users;
        $request->merge([$model => $users]);

        return $request;
    }

    /**
     * Get paginate for certifies' page.
     *
     * @param Request $request
     * @param $filter
     * @param integer $maxResults
     * @param string $model
     * @return Request
     */
    protected function getCertifyControllerPaginate($request, $filter = null, $maxResults = 30, $model = 'certifies') {
        $user = Auth::user();
        
        if(empty($filter)) {
            if ($user->isAdmin()) {
                $certifies = Certify::paginate($maxResults);
            }else{
                $certifies = Certify::where(['cpf' => $user->cpf])->paginate($maxResults);
            }
        }else{
            if ($user->isAdmin()) {
                $certifies = Certify::where('name', 'like', '%' . $filter . '%')
                    ->orWhere('title', 'like', '%' . $filter . '%');
            }else{
                $certifies = Certify::where(['cpf' => $user->cpf, ['title', 'like', '%' . $filter . '%']]);
            }
            $certifies = $certifies->paginate($maxResults);
        }
        $this->model = $certifies;
        $request->merge([$model => $certifies]);

        return $request;
    }
}
