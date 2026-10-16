<?php

namespace Modules\Web\Controllers;

use App\Controllers\BaseController;

class CekTagihan extends BaseController
{
    public function index()
    {
        $q = $this->request->getUri()->getQuery();
        return redirect()->to(base_url('spp') . ($q ? '?' . $q : ''));
    }
}
