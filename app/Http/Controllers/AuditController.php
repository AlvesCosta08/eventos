<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class AuditController extends Controller
{
    /**
     * Exibe o histórico de auditoria do sistema.
     * Apenas Administradores Gerais podem acessar.
     */
    public function index(Request $request): View
    {
        // Garante que apenas o Admin Geral veja isso (segurança extra além da rota)
        abort_unless($request->user()->isAdminGeral(), 403);

        // Carrega as auditorias com os relacionamentos de usuário e do modelo auditado
        $audits = Audit::with(['user', 'auditable'])
            ->latest('created_at')
            ->paginate(20);

        // ✅ CORREÇÃO PHPStan: Informa que esta string é uma view-string válida
        /** @var view-string $viewName */
        $viewName = 'audits.index';

        return view($viewName, compact('audits'));
    }
}