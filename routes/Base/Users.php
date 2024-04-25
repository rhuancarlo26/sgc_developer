<?php

use Illuminate\Support\Facades\Route;
use App\Shared\Base\User\Controllers\UserController;
use App\Shared\Base\Role\Controllers\RoleController;

Route::prefix('cadastros')->group(function () {

    // Usuários
    Route::prefix('usuarios')->group(function () {
        Route::get('/',                     [UserController::class, 'index'])->name('cadastros.usuarios.listagem');
        Route::get('/formulario/{user?}',   [UserController::class, 'create'])->name('cadastros.usuarios.formulario');
        Route::post('/criar',               [UserController::class, 'store'])->name('cadastros.usuarios.criar');
        Route::patch('/atualizar/{user}',   [UserController::class, 'update'])->name('cadastros.usuarios.atualizar');
        Route::delete('/deletar/{user}',    [UserController::class, 'destroy'])->name('cadastros.usuarios.deletar');
    }); // FIM USUARIOS

    // Perfis
    Route::prefix('perfis')->group(function () {
        Route::get('/',                     [RoleController::class, 'index'])->name('cadastros.perfis.listagem');
        Route::get('/formulario/{role?}',   [RoleController::class, 'create'])->name('cadastros.perfis.formulario');
        Route::post('/criar',               [RoleController::class, 'store'])->name('cadastros.perfis.criar');
        Route::patch('/atualizar/{role}',   [RoleController::class, 'update'])->name('cadastros.perfis.atualizar');
        Route::delete('/deletar/{role}',    [RoleController::class, 'destroy'])->name('cadastros.perfis.deletar');
    });
});
