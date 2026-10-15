<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * SmartStock is local-first: readings, alerts and sensor state live in the
 * browser (localStorage, see public/assets/js/smartstock.js). The server only
 * serves the page shells, so every page keeps working offline once loaded.
 */
class SmartStockController extends Controller
{
    private function shell($page, $title)
    {
        $this->call->view('smartstock/shell', ['page' => $page, 'title' => $title]);
    }

    public function dashboard()  { $this->shell('dashboard',  'Corrosion Risk Dashboard'); }
    public function monitoring() { $this->shell('monitoring', 'Environmental Monitoring'); }
    public function corrosion()  { $this->shell('corrosion',  'Corrosion Risk'); }
    public function alerts()     { $this->shell('alerts',     'Alerts & Notification'); }
}
