<?php
// app/Http/Middleware/LogActivity.php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // FIXED: Check each method individually, not as an array
        if (Auth::check() && ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('patch') || $request->isMethod('delete'))) {
            try {
                $action = $this->getActionFromRoute($request);
                
                if ($action) {
                    ActivityLog::log(
                        $action,
                        $this->getActionDescription($action, $request),
                        null,
                        [
                            'url' => $request->fullUrl(),
                            'method' => $request->method(),
                            'input' => $this->sanitizeInput($request->all()),
                        ]
                    );
                }
            } catch (Exception $e) {
                // Don't let logging errors break the application
                \Log::error('Activity logging failed', [
                    'error' => $e->getMessage(),
                    'route' => $request->route()?->getName(),
                    'user_id' => Auth::id(),
                ]);
            }
        }

        return $response;
    }

    private function getActionFromRoute(Request $request)
    {
        $route = $request->route();
        if (!$route) return null;

        $routeName = $route->getName();
        if (!$routeName) return null;

        // Map route names to actions
        $actionMap = [
            'documents.store' => 'document_created',
            'documents.update' => 'document_updated',
            'documents.destroy' => 'document_deleted',
            'documents.status.update' => 'document_status_updated', // Updated for your route names
            'document-routes.store' => 'document_routed',
            'documents.routing.store' => 'document_routed',
            'users.store' => 'user_created',
            'users.update' => 'user_updated',
            'login' => 'user_login',
            'logout' => 'user_logout',
            'admin.settings.update' => 'settings_updated',
            'admin.settings.store' => 'setting_created',
        ];

        return $actionMap[$routeName] ?? null;
    }

    private function getActionDescription($action, Request $request)
    {
        $descriptions = [
            'document_created' => 'Created a new document',
            'document_updated' => 'Updated document information',
            'document_deleted' => 'Deleted a document',
            'document_status_updated' => 'Updated document status',
            'document_routed' => 'Routed document to another department',
            'user_created' => 'Created a new user account',
            'user_updated' => 'Updated user information',
            'user_login' => 'Logged into the system',
            'user_logout' => 'Logged out of the system',
            'settings_updated' => 'Updated system settings',
            'setting_created' => 'Created new system setting',
        ];

        return $descriptions[$action] ?? $action;
    }

    private function sanitizeInput(array $input)
    {
        // Remove sensitive data from logging
        $sensitiveFields = [
            'password', 
            'password_confirmation', 
            'current_password', 
            '_token',
            '_method',
            'files', // Don't log file uploads
        ];
        
        foreach ($sensitiveFields as $field) {
            unset($input[$field]);
        }

        // Truncate large inputs to prevent database issues
        array_walk_recursive($input, function (&$value) {
            if (is_string($value) && strlen($value) > 1000) {
                $value = substr($value, 0, 1000) . '... [truncated]';
            }
        });

        return $input;
    }
}