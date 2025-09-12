<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use App\AccessControl;
use Auth;

class PermissionController extends Controller
{
    public function index($role_id = '')
    {
        $permission_list = $this->getPermissionListByRole($role_id);

        $notallowed = $this->getNotAllowedControllers();

        if (has_membership_system() == 'enabled') {
            $company = Auth::user()->company;
            $notallowed = array_merge($notallowed, $this->getNotAllowedByPackage($company));
        }

        $routes = $this->getAvailableRoutes($notallowed);

        $permission = $this->filterPermissions($routes);

        return view('backend.permission.create', compact('permission', 'permission_list', 'role_id'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required',
            'permissions' => 'required'
        ]);

        AccessControl::where('role_id', $request->role_id)->delete();
        foreach ($request->permissions as $role) {
            $this->storePermission($request->role_id, $role);
        }

        return redirect('permission/control')->with('success', _lang('Saved Successfully'));
    }



    private function getPermissionListByRole($role_id)
    {
        if ($role_id !== '') {
            return AccessControl::where("role_id", $role_id)->pluck('permission')->toArray();
        }
        return [];
    }


    private function getNotAllowedControllers()
    {
        return [
            '\App\Http\Controllers\Auth\LoginController',
            'App\Http\Controllers\Auth\RegisterController',
            'App\Http\Controllers\Auth\ForgotPasswordController',
            'App\Http\Controllers\Auth\ResetPasswordController',
            'App\Http\Controllers\Auth\VerificationController',
            'App\Http\Controllers\DashboardController',
            'App\Http\Controllers\EmailSubscriberController',
            'App\Http\Controllers\ProfileController',
            'App\Http\Controllers\UserController',
            'App\Http\Controllers\LanguageController',
            'App\Http\Controllers\UtilityController',
            'App\Http\Controllers\StaffController',
            'App\Http\Controllers\RoleController',
            'App\Http\Controllers\EmailTemplateController',
            'App\Http\Controllers\PackageController',
            'App\Http\Controllers\PaymentController',
            'App\Http\Controllers\FaqController',
            'App\Http\Controllers\FeatureController',
            'App\Http\Controllers\MembershipController',
            'App\Http\Controllers\CronJobsController',
            'App\Http\Controllers\ChatController',
            'App\Http\Controllers\PermissionController',
            'App\Http\Controllers\API\UserController',
            'App\Http\Controllers\Install\InstallController',
            'App\Http\Controllers\Install\UpdateController',
        ];
    }


    private function getNotAllowedByPackage($company)
    {
        $package_fields = [
            'contacts_limit' => 'App\Http\Controllers\ContactController',
            'invoice_limit' => 'App\Http\Controllers\InvoiceController',
            'quotation_limit' => 'App\Http\Controllers\QuotationController',
            'project_management_module' => [
                'App\Http\Controllers\ProjectController',
                'App\Http\Controllers\LeadController',
                'App\Http\Controllers\TaskController',
                'App\Http\Controllers\LeadSourceController',
                'App\Http\Controllers\LeadStatusController',
                'App\Http\Controllers\ProjectMilestoneController',
                'App\Http\Controllers\TaskStatusController',
                'App\Http\Controllers\TimeSheetController',
            ],
            'recurring_transaction' => [
                'App\Http\Controllers\RepeatingExpenseController',
                'App\Http\Controllers\RepeatingIncomeController',
            ],
            'file_manager' => 'App\Http\Controllers\FileManagerController',
            'inventory_module' => [
                'App\Http\Controllers\PurchaseController',
                'App\Http\Controllers\SalesReturnController',
            ],
        ];

        $notallowed = [];
        foreach ($package_fields as $key => $value) {
            if ($company->$key == 'No') {
                $notallowed = array_merge($notallowed, is_array($value) ? $value : [$value]);
            }
        }

        return $notallowed;
    }


    private function getAvailableRoutes($notallowed)
    {
        $app = app();
        $routeCollection = $app->routes->getRoutes();
        $routes = [];

        foreach ($routeCollection as $route) {
            $action = $route->getAction();
            if (isset($action['controller'])) {
                $explodedAction = explode('@', $action['controller']);
                if (in_array($explodedAction[0], $notallowed)) {
                    continue;
                }
                $this->addRouteIfValid($routes, $explodedAction, $route);
            }
        }

        return $routes;
    }


    private function addRouteIfValid(&$routes, $explodedAction, $route)
    {
        if (!isset($routes[$explodedAction[0]])) {
            $routes[$explodedAction[0]] = [];
        }

        $controller = new $explodedAction[0]();
        if (method_exists($controller, $explodedAction[1])) {
            $routes[$explodedAction[0]][] = [
                "method" => $explodedAction[1],
                "action" => $route->action
            ];
        }
    }


    private function filterPermissions($routes)
    {
        $ignoreRoute = [
            //'events.show',
            //'notices.show',
        ];

        $permission = [];

        foreach ($routes as $key => $route) {
            foreach ($route as $r) {
                if (strpos($r['method'], 'get') === 0) {
                    continue;
                }

                if (isset($r['action']['as'])) {
                    $routeName = $r['action']['as'];
                    if (!in_array($routeName, $ignoreRoute)) {
                        $permission[$key][$routeName] = $r['method'];
                    }
                }
            }
        }

        return $this->optimizePermissions($permission);
    }


    private function optimizePermissions($permission)
    {
        foreach ($permission as $key => $val) {
            foreach ($val as $name => $url) {
                if ($url == "store" && in_array("create", $val)) {
                    unset($permission[$key][$name]);
                }
                if ($url == "update" && in_array("edit", $val)) {
                    unset($permission[$key][$name]);
                }
            }
        }
        return $permission;
    }


    private function storePermission($role_id, $permission)
    {
        $accessControl = new AccessControl();
        $accessControl->role_id = $role_id;
        $accessControl->permission = $permission;
        $accessControl->save();
    }
}
