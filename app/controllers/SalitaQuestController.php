<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class SalitaQuestController extends Controller
{
    // Prototype: serves the Salita Quest UI. Data is still hard-coded in the view;
    // the words/submissions tables (migration 003) are ready for wiring real endpoints.
    public function index() {
        $this->call->view('salita_quest');
    }
}
