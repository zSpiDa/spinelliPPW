<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return '<h1>Benvenuto nel Gestionale del Gruppo di Ricerca</h1>
        <p>Questa pagina è gestita da un controller Laravel</p>';
    }

    public function about()
    {
        return '<h1>Chi siamo?</h1>
        <p>Questo gestionale supporta la gestione di progetti e pubblicazioni del gruppo IVU Lab.</p>';
    }

    public function projects()
    {
        $projects = ['Morfeo', 'Geologia', 'Astroworld'];
        $html = '<h1>Progetti di Ricerca</h1><ul>';
        foreach ($projects as $p) {
            $html .= '<li>' . $p . '</li>';
        }
        $html .= '</ul>';
        return $html;
    }


    public function showProjects(string $name)
    {
        return '<h1>Progetto ' . $name . '</h1>';
    }

}
